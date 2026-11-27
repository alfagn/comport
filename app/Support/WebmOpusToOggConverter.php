<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Pure-PHP RFC 7845 / RFC 3533 remuxer that extracts Opus audio frames from
 * browser-recorded WebM/Matroska (`audio/webm;codecs=opus`) streams and wraps
 * them into a spec-compliant Ogg Opus (`OggS`) container accepted by Meta
 * WhatsApp Cloud API (`voice: true`), with zero external binary dependencies.
 */
class WebmOpusToOggConverter
{
    /** @var array<int, int>|null */
    private static ?array $crcTable = null;

    /**
     * Remux a WebM Opus file on disk into a temporary `.ogg` Opus file.
     * Returns the absolute path to the generated `.ogg` file, or null if input is not WebM Opus.
     */
    public static function convertFile(string $sourcePath): ?string
    {
        if (! is_file($sourcePath)) {
            return null;
        }

        $bytes = (string) file_get_contents($sourcePath);
        $oggBytes = self::convertBytes($bytes);

        if ($oggBytes === null) {
            return null;
        }

        $targetPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'wa_opus_'.Str::uuid().'.ogg';
        if (file_put_contents($targetPath, $oggBytes) === false) {
            return null;
        }

        return $targetPath;
    }

    /**
     * Convert raw WebM Opus bytes into Ogg Opus bytes (or pass through if already `OggS`).
     */
    public static function convertBytes(string $data): ?string
    {
        $len = strlen($data);
        if ($len < 16) {
            return null;
        }

        if (str_starts_with($data, 'OggS')) {
            return $data;
        }

        // EBML magic bytes: 0x1A 0x45 0xDF 0xA3
        if (substr($data, 0, 4) !== "\x1A\x45\xDF\xA3") {
            return null;
        }

        $pos = 0;
        $channels = 1;
        $opusHead = null;
        $packets = [];

        $masterIds = [
            0x18538067 => true, // Segment
            0x1654AE6B => true, // Tracks
            0xAE => true,       // TrackEntry
            0xE1 => true,       // Audio
            0x1F43B675 => true, // Cluster
            0xA0 => true,       // BlockGroup
        ];

        while ($pos < $len) {
            $idRes = self::readEbmlId($data, $pos, $len);
            if ($idRes === null) {
                break;
            }
            [$id, $pos] = $idRes;

            $sizeRes = self::readEbmlSize($data, $pos, $len);
            if ($sizeRes === null) {
                break;
            }
            [$size, $pos] = $sizeRes;

            if (isset($masterIds[$id])) {
                continue;
            }

            if ($size < 0 || $pos + $size > $len) {
                break;
            }

            $payload = substr($data, $pos, $size);
            $pos += $size;

            if ($id === 0x63A2 && $size >= 19 && str_starts_with($payload, 'OpusHead')) {
                $opusHead = $payload;
            } elseif ($id === 0x9F && $size >= 1) {
                $channels = max(1, min(2, ord($payload[0])));
            } elseif ($id === 0xA3 || $id === 0xA1) {
                foreach (self::extractBlockPackets($payload) as $pkt) {
                    if ($pkt !== '') {
                        $packets[] = $pkt;
                    }
                }
            }
        }

        if ($packets === []) {
            return null;
        }

        if ($opusHead === null) {
            $opusHead = 'OpusHead'
                ."\x01"
                .chr($channels)
                .pack('v', 312)
                .pack('V', 48000)
                .pack('v', 0)
                ."\x00";
        }

        $vendor = 'Empetix';
        $opusTags = 'OpusTags'.pack('V', strlen($vendor)).$vendor.pack('V', 0);

        $serial = random_int(1, 0x7FFFFFFF);
        $seq = 0;
        $out = '';

        // Page 0: BOS OpusHead
        $out .= self::buildOggPage([$opusHead], 0x02, 0, $serial, $seq++);

        // Page 1: OpusTags
        $out .= self::buildOggPage([$opusTags], 0x00, 0, $serial, $seq++);

        // Audio Pages (~25 Opus packets per page, ~500ms each)
        $granulePos = 312;
        $chunks = array_chunk($packets, 25);
        $lastChunkIdx = count($chunks) - 1;

        foreach ($chunks as $idx => $chunkPackets) {
            foreach ($chunkPackets as $pkt) {
                $granulePos += self::opusPacketSamples($pkt);
            }

            $flag = ($idx === $lastChunkIdx) ? 0x04 : 0x00;
            $out .= self::buildOggPage($chunkPackets, $flag, $granulePos, $serial, $seq++);
        }

        return $out;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private static function readEbmlId(string $data, int $pos, int $len): ?array
    {
        if ($pos >= $len) {
            return null;
        }

        $b0 = ord($data[$pos]);
        if ($b0 === 0) {
            return null;
        }

        $width = 1;
        $mask = 0x80;
        while ($width <= 4 && ($b0 & $mask) === 0) {
            $width++;
            $mask >>= 1;
        }

        if ($width > 4 || $pos + $width > $len) {
            return null;
        }

        $val = 0;
        for ($i = 0; $i < $width; $i++) {
            $val = ($val << 8) | ord($data[$pos + $i]);
        }

        return [$val, $pos + $width];
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private static function readEbmlSize(string $data, int $pos, int $len): ?array
    {
        if ($pos >= $len) {
            return null;
        }

        $b0 = ord($data[$pos]);
        if ($b0 === 0) {
            return null;
        }

        $width = 1;
        $mask = 0x80;
        while ($width <= 8 && ($b0 & $mask) === 0) {
            $width++;
            $mask >>= 1;
        }

        if ($width > 8 || $pos + $width > $len) {
            return null;
        }

        $val = $b0 & ($mask - 1);
        $allOnes = $mask - 1;

        for ($i = 1; $i < $width; $i++) {
            $byte = ord($data[$pos + $i]);
            $val = ($val << 8) | $byte;
            $allOnes = ($allOnes << 8) | 0xFF;
        }

        if ($val === $allOnes) {
            return [-1, $pos + $width];
        }

        return [$val, $pos + $width];
    }

    /**
     * Extract individual Opus frames from a Matroska `SimpleBlock` / `Block` payload.
     *
     * @return list<string>
     */
    private static function extractBlockPackets(string $block): array
    {
        $bLen = strlen($block);
        if ($bLen < 4) {
            return [];
        }

        $trackRes = self::readEbmlSize($block, 0, $bLen);
        if ($trackRes === null) {
            return [];
        }
        [, $offset] = $trackRes;

        // 2 bytes int16 timecode + 1 byte flags
        if ($offset + 3 > $bLen) {
            return [];
        }

        $flags = ord($block[$offset + 2]);
        $offset += 3;

        $lacing = ($flags >> 1) & 0x03;

        if ($lacing === 0) {
            return [substr($block, $offset)];
        }

        if ($offset >= $bLen) {
            return [];
        }

        $frameCount = ord($block[$offset]) + 1;
        $offset++;

        if ($lacing === 2) {
            // Fixed-size lacing
            $remaining = $bLen - $offset;
            if ($frameCount <= 0 || $remaining <= 0) {
                return [];
            }
            $frameSize = intdiv($remaining, $frameCount);
            if ($frameSize <= 0) {
                return [];
            }

            $out = [];
            for ($i = 0; $i < $frameCount; $i++) {
                $out[] = substr($block, $offset + ($i * $frameSize), $frameSize);
            }

            return $out;
        }

        if ($lacing === 1) {
            // Xiph lacing
            $sizes = [];
            for ($i = 0; $i < $frameCount - 1; $i++) {
                $sz = 0;
                while ($offset < $bLen) {
                    $b = ord($block[$offset++]);
                    $sz += $b;
                    if ($b < 255) {
                        break;
                    }
                }
                $sizes[] = $sz;
            }

            $out = [];
            foreach ($sizes as $sz) {
                if ($offset + $sz > $bLen) {
                    break;
                }
                $out[] = substr($block, $offset, $sz);
                $offset += $sz;
            }
            if ($offset < $bLen) {
                $out[] = substr($block, $offset);
            }

            return $out;
        }

        // EBML lacing (lacing === 3)
        $sizes = [];
        $first = self::readEbmlSize($block, $offset, $bLen);
        if ($first === null) {
            return [];
        }
        [$prevSize, $offset] = $first;
        $sizes[] = $prevSize;

        for ($i = 1; $i < $frameCount - 1; $i++) {
            $b0 = $offset < $bLen ? ord($block[$offset]) : 0;
            if ($b0 === 0) {
                break;
            }
            $width = 1;
            $mask = 0x80;
            while ($width <= 8 && ($b0 & $mask) === 0) {
                $width++;
                $mask >>= 1;
            }
            $res = self::readEbmlSize($block, $offset, $bLen);
            if ($res === null) {
                break;
            }
            [$rawDelta, $offset] = $res;
            $bias = (1 << ((7 * $width) - 1)) - 1;
            $prevSize += ($rawDelta - $bias);
            $sizes[] = max(0, $prevSize);
        }

        $out = [];
        foreach ($sizes as $sz) {
            if ($offset + $sz > $bLen) {
                break;
            }
            $out[] = substr($block, $offset, $sz);
            $offset += $sz;
        }
        if ($offset < $bLen) {
            $out[] = substr($block, $offset);
        }

        return $out;
    }

    /**
     * Compute 48kHz PCM sample count for a single RFC 6716 Opus packet.
     */
    private static function opusPacketSamples(string $packet): int
    {
        if ($packet === '') {
            return 960;
        }

        $toc = ord($packet[0]);
        $config = ($toc >> 3) & 0x1F;

        if ($config >= 16) {
            $frameSize = [120, 240, 480, 960][$config & 0x03];
        } elseif ($config >= 12) {
            $frameSize = [480, 960][$config & 0x01];
        } else {
            $frameSize = [480, 960, 1920, 2880][$config & 0x03];
        }

        $countCode = $toc & 0x03;
        $frames = match ($countCode) {
            0 => 1,
            1, 2 => 2,
            default => strlen($packet) > 1 ? max(1, ord($packet[1]) & 0x3F) : 1,
        };

        return $frameSize * $frames;
    }

    /**
     * Build a single RFC 3533 Ogg page with direct `0x04C11DB7` CRC-32 checksum.
     *
     * @param  list<string>  $packets
     */
    private static function buildOggPage(
        array $packets,
        int $headerFlag,
        int $granulePos,
        int $serial,
        int $pageSeq,
    ): string {
        $segments = '';
        $body = '';

        foreach ($packets as $pkt) {
            $rem = strlen($pkt);
            while ($rem >= 255) {
                $segments .= "\xFF";
                $rem -= 255;
            }
            $segments .= chr($rem);
            $body .= $pkt;
        }

        $segCount = strlen($segments);

        $header = 'OggS'
            ."\x00"
            .chr($headerFlag)
            .pack('P', $granulePos)
            .pack('V', $serial)
            .pack('V', $pageSeq)
            ."\x00\x00\x00\x00"
            .chr($segCount)
            .$segments;

        $page = $header.$body;
        $crc = self::oggCrc32($page);

        return substr_replace($page, pack('V', $crc), 22, 4);
    }

    private static function oggCrc32(string $page): int
    {
        if (self::$crcTable === null) {
            $table = [];
            for ($i = 0; $i < 256; $i++) {
                $r = $i << 24;
                for ($j = 0; $j < 8; $j++) {
                    $r = ($r & 0x80000000) !== 0
                        ? ((($r << 1) ^ 0x04C11DB7) & 0xFFFFFFFF)
                        : (($r << 1) & 0xFFFFFFFF);
                }
                $table[$i] = $r;
            }
            self::$crcTable = $table;
        }

        $table = self::$crcTable;
        $crc = 0;
        $len = strlen($page);

        for ($i = 0; $i < $len; $i++) {
            $crc = ((($crc << 8) & 0xFFFFFFFF) ^ $table[(($crc >> 24) ^ ord($page[$i])) & 0xFF]) & 0xFFFFFFFF;
        }

        return $crc;
    }
}

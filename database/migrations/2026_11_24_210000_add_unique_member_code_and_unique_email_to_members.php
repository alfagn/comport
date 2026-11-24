<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('members', 'member_code')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('member_code', 32)->nullable()->after('id');
            });
        }

        // Normalize emails & backfill unique member_code for all existing members
        $members = DB::table('members')->orderBy('sort_order')->orderBy('id')->get();
        $seenEmails = [];
        $seq = 1;

        foreach ($members as $member) {
            $code = $member->member_code ?: ('EMP-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT));
            while (DB::table('members')->where('member_code', $code)->where('id', '!=', $member->id)->exists()) {
                $seq++;
                $code = 'EMP-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            }
            $seq++;

            $rawEmail = is_string($member->email) ? strtolower(trim($member->email)) : '';
            if ($rawEmail === '' || str_contains($rawEmail, '&#64;') || $rawEmail === 'hello@empetix.com') {
                $slugBase = $member->slug ?: Str::slug($member->name) ?: ('member-'.$member->id);
                $rawEmail = $slugBase.'@empetix.com';
            }

            if (isset($seenEmails[$rawEmail])) {
                [$local, $domain] = array_pad(explode('@', $rawEmail, 2), 2, 'empetix.com');
                $rawEmail = "{$local}-{$member->id}@{$domain}";
            }
            $seenEmails[$rawEmail] = true;

            DB::table('members')->where('id', $member->id)->update([
                'member_code' => $code,
                'email' => $rawEmail,
            ]);
        }

        Schema::table('members', function (Blueprint $table) {
            $table->unique('member_code', 'members_member_code_unique');
            $table->unique('email', 'members_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique('members_member_code_unique');
            $table->dropUnique('members_email_unique');
            $table->dropColumn('member_code');
        });
    }
};

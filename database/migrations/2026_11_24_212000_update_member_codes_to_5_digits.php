<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $members = DB::table('members')->orderBy('id')->get();

        foreach ($members as $member) {
            if ($member->member_code && preg_match('/^EMP-(\d+)$/i', $member->member_code, $m)) {
                $num = (int) $m[1];
                $newCode = 'EMP-'.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
                DB::table('members')->where('id', $member->id)->update([
                    'member_code' => $newCode,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Reversible if needed
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
        });

        // Backfill username for existing records so they can login with username immediately.
        $users = DB::table('users')->select('id', 'name', 'email', 'username')->get();

        foreach ($users as $user) {
            if (!empty($user->username)) {
                continue;
            }

            $base = Str::slug((string) Str::before((string) $user->email, '@'), '_');
            if ($base === '') {
                $base = Str::slug((string) $user->name, '_');
            }
            if ($base === '') {
                $base = 'user';
            }

            $candidate = $base;
            $suffix = 1;

            while (DB::table('users')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                $suffix++;
                $candidate = $base . '_' . $suffix;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }
        });
    }
};

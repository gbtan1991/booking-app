<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy role column (and its index) first, then add new columns
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop the index before dropping the column on SQLite
                try { $table->dropIndex('users_role_index'); } catch (\Throwable) {}
                $table->dropColumn('role');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'system_role')) {
                $table->string('system_role')->default('Administrator')->after('position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(['position', 'system_role'], fn ($c) => Schema::hasColumn('users', $c)));
        });
    }
};

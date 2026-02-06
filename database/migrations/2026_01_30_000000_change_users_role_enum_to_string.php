<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50) USING role::text");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET NOT NULL");
        } else {
            throw new \RuntimeException('Migration đổi role sang string chỉ hỗ trợ MySQL và PostgreSQL.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_main','admin_sub','user') NOT NULL DEFAULT 'user'");
        }
    }
};

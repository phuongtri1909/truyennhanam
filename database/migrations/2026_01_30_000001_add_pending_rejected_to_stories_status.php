<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE stories MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'rejected') NOT NULL DEFAULT 'draft'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE stories DROP CONSTRAINT IF EXISTS stories_status_check");
            DB::statement("ALTER TABLE stories ADD CONSTRAINT stories_status_check CHECK (status IN ('draft', 'pending', 'published', 'rejected'))");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE stories SET status = 'draft' WHERE status IN ('pending', 'rejected')");
            DB::statement("ALTER TABLE stories MODIFY COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
        }
    }
};

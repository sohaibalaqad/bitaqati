<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the role enum to include super_admin and network_admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','network_admin','admin','client') NOT NULL DEFAULT 'client'");

        // Rename existing 'admin' → 'network_admin'
        DB::statement("UPDATE users SET role = 'network_admin' WHERE role = 'admin'");

        // Now remove the legacy 'admin' value from the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','network_admin','client') NOT NULL DEFAULT 'client'");
    }

    public function down(): void
    {
        // Allow adding 'admin' back temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','network_admin','admin','client') NOT NULL DEFAULT 'client'");
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'network_admin'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','client') NOT NULL DEFAULT 'client'");
    }
};

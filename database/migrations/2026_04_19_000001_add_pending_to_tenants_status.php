<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'pending' value to the tenants status ENUM
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active','suspended','trial','pending') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Move any pending tenants to suspended before removing the value
        DB::statement("UPDATE tenants SET status = 'suspended' WHERE status = 'pending'");
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active','suspended','trial') NOT NULL DEFAULT 'active'");
    }
};

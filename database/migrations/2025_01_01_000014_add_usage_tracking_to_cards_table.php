<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cards', function (Blueprint $table) {
            $table->timestamp('first_used_at')->nullable()->after('sold_at');
            $table->boolean('is_used')->default(false)->after('first_used_at');
        });
    }
    public function down(): void {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['first_used_at','is_used']);
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'subject')) {
                $table->string('subject')->nullable()->after('title');
            }
            if (!Schema::hasColumn('tickets', 'category')) {
                $table->string('category')->default('general')->after('subject');
            }
        });
    }
    public function down(): void {}
};

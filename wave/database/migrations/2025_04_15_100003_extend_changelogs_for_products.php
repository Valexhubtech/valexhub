<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('changelogs', function (Blueprint $table) {
            $table->string('loggable_type')->nullable()->after('id');
            $table->unsignedInteger('loggable_id')->nullable()->after('loggable_type');
            $table->index(['loggable_type', 'loggable_id'], 'idx_loggable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('changelogs', function (Blueprint $table) {
            $table->dropIndex('idx_loggable');
            $table->dropColumn(['loggable_type', 'loggable_id']);
        });
    }
};
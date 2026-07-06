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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('referrer_id')->nullable()->after('id');
            $table->string('referral_code')->nullable()->unique()->after('referrer_id');

            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('set null');
            $table->index('referrer_id', 'idx_users_referrer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropIndex('idx_users_referrer');
            $table->dropColumn(['referrer_id', 'referral_code']);
        });
    }
};

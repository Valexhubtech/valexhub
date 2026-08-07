<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('internship_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('roles');
            $table->date('application_deadline')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('internship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_session_id')->constrained('internship_sessions')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('institution');
            $table->string('course');
            $table->string('graduation_year', 4);
            $table->string('role');
            $table->text('cover_letter')->nullable();
            $table->string('cv_path');
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_applications');
        Schema::dropIfExists('internship_sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('resume_filename')->nullable()->after('resume_url');
            $table->string('resume_mime')->nullable()->after('resume_filename');
            $table->unsignedBigInteger('resume_size')->nullable()->after('resume_mime');
            $table->longText('resume_data')->nullable()->after('resume_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['resume_filename', 'resume_mime', 'resume_size', 'resume_data']);
        });
    }
};

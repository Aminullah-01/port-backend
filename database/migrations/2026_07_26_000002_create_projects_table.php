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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->json('technologies')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('thumbnail')->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->string('status')->default('published')->index();
            $table->string('github_url')->nullable();
            $table->string('live_url')->nullable();
            $table->integer('display_order')->default(0)->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'status', 'display_order']);
            $table->index(['featured', 'status']);
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('projects');
    }
};

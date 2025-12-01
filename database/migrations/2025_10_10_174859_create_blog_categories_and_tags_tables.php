<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blog Categories
        if (! Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('status')->default('Aktif');
                $table->integer('display_order')->default(0); // Context7: order → display_order
                $table->timestamps();
                $table->softDeletes();

                $table->index('status');
            });
        }

        // Blog Tags
        if (! Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('status')->default('Aktif');
                $table->timestamps();
                $table->softDeletes();

                $table->index('status');
            });
        }

        // Blog Post Tags Pivot
        if (! Schema::hasTable('blog_post_tags')) {
            Schema::create('blog_post_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('blog_posts')->onDelete('cascade');
                $table->foreignId('tag_id')->constrained('blog_tags')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['post_id', 'tag_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tags');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};

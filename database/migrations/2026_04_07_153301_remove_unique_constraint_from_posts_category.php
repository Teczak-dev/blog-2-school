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
        Schema::table('posts', function (Blueprint $table) {
            // Drop the unique constraint on category column
            // This constraint was leftover from when the column was called 'slug'
            $table->dropIndex('posts_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Add back the unique constraint if needed to roll back
            $table->unique('category', 'posts_slug_unique');
        });
    }
};

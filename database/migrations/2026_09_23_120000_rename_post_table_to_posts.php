<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * The original migration created a table called `post`, but the Posts model
 * (and every query) uses `posts`. Rename it if, and only if, it is still
 * called `post`, so databases that already have `posts` are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('post') && ! Schema::hasTable('posts')) {
            Schema::rename('post', 'posts');
        }
    }

    public function down(): void
    {
        // Intentionally empty: `posts` is the correct name.
    }
};

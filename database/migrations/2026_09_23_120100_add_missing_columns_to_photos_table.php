<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Photos model (and the profile page) use idusers / phototitle /
 * photodescription / photoblob, but the original migration only created
 * `id` + timestamps, so a fresh database crashed on /@username.
 *
 * Each column is added only if it is missing, so a database where they were
 * already added by hand is left exactly as it is.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            if (! Schema::hasColumn('photos', 'idusers')) {
                $table->unsignedBigInteger('idusers')->nullable()->index();
            }
            if (! Schema::hasColumn('photos', 'phototitle')) {
                $table->string('phototitle')->nullable();
            }
            if (! Schema::hasColumn('photos', 'photodescription')) {
                $table->text('photodescription')->nullable();
            }
            if (! Schema::hasColumn('photos', 'photoblob')) {
                $table->string('photoblob')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty: these columns are part of the model's contract.
    }
};

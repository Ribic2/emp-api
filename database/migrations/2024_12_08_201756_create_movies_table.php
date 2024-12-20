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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_title_id')->unique();
            $table->string('original_title');
            $table->integer('year');
            $table->integer('duration');
            $table->longText('description')->nullable();
            $table->string('director');
            $table->longText('writers');
            $table->longText('actors');
            $table->decimal('avg_vote', 3, 1)->nullable();
            $table->integer('votes')->nullable();
            $table->foreignId('production_company_id')->references('id')->on('production_companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};

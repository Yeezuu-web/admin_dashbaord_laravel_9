<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenreMoviePivotTable extends Migration
{
    public function up()
    {
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->unsignedBigInteger('movie_id');
            $table->foreign('movie_id', 'movie_id_fk_7047588')->references('id')->on('movies')->onDelete('cascade');
            $table->unsignedBigInteger('genre_id');
            $table->foreign('genre_id', 'genre_id_fk_7047588')->references('id')->on('genres')->onDelete('cascade');
        });
    }
}
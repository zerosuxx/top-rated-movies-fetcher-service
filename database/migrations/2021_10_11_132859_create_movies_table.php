<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('length');
            $table->date('release_date');
            $table->text('overview');
            $table->string('poster_url');
            $table->bigInteger('tmdb_id');
            $table->unique('tmdb_id');
            $table->float('tmdb_vote_average');
            $table->bigInteger('tmdb_vote_count');
            $table->string('tmdb_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}

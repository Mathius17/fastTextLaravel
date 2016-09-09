<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTweets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('tweet_id')->unsigned();
            $table->bigInteger('user')->unsigned();
            $table->text('text');
            $table->string('source');
            $table->integer('favorite_count')->unsigned()->nullable();
            $table->integer('retweet_count')->unsigned()->nullable();
            $table->string('lang')->nullable();
            $table->dateTime('created_at');
            $table->integer('movie_id')->unsigned();
            
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweets');
    }
}

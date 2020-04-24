<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('slug', 300)->unique();
            $table->text('description');
            $table->string('short_description', 255);
            $table->string('street', 400);
            $table->string('street2', 400);
            $table->string('city', 100);
            $table->string('state', 3);
            $table->string('postal', 20);
            $table->string('country', 3);
            $table->string('lat', 3);
            $table->string('lng', 3);
            $table->text('image');
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('locations');
    }
}

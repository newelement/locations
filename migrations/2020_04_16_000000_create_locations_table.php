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
            $table->text('description')->nullable();
            $table->string('short_description', 255)->nullable();
            $table->string('street', 400)->nullable();
            $table->string('street2', 400)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 3)->nullable();
            $table->string('postal', 20)->nullable();
            $table->string('country', 3)->nullable();
            $table->string('website', 300)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 300)->nullable();
            $table->integer('location_level_id')->nullable();
            $table->decimal('lat', 10,8)->nullable();
            $table->decimal('lng', 11,8)->nullable();
            $table->text('image')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
            $table->index('location_level_id');
            $table->index('sort');
            $table->index('lat');
            $table->index('lng');
            $table->index('title');
            $table->index('street');
            $table->index('state');
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

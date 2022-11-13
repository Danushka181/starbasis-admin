<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentersAndAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centers_and_areas', function (Blueprint $table) {
            $table->id();
            $table->string('center_name', 100)->unique();
            $table->foreignId('user_id');
            $table->string('center_address');
            $table->string('status')->nullable();
            $table->timestamps();
            // Make a relation with user id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('centers_and_areas');
    }
}

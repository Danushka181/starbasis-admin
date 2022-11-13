<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('book_name', 100);
            $table->foreignId('category');
            $table->string('image')->nullable();
            $table->foreignId('book_id');
            $table->text('description');
            $table->string('auther_name');
            $table->bigInteger('books_count')->nullable();
            $table->bigInteger('book_price')->nullable();
            $table->bigInteger('rack_id')->nullable();
            $table->bigInteger('row_id')->nullable();  

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category')->references('id')->on('books_categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books_details');
    }
}

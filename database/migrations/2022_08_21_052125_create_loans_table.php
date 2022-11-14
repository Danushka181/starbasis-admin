<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('l_amount');
            $table->string('l_pending_amount')->nullable();
            $table->string('l_duration');
            $table->string('l_status');
            $table->string('l_installment');
            $table->string('l_customer');
            $table->string('l_product');
            $table->string('l_start');
            $table->string('l_end');
            $table->string('l_last_payment')->nullable();
            $table->string('l_installment_count');
            $table->string('l_document_charge');
            $table->string('l_stage')->nullable();
            $table->string('status');

            $table->string('user');
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('l_product')->references('id')->on('loan_products')->onDelete('cascade');
            $table->foreign('l_customer')->references('id')->on('customer_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Personal details
            $table->string('c_name');
            $table->string('c_address');
            $table->string('c_bday');
            $table->string('c_age');
            $table->string('c_id_number', 100)->unique();
            $table->string('c_mobile_number');
            $table->string('c_land_number')->nullable();
            $table->string('c_month_income');
            $table->string('c_ceb_number');
            $table->string('c_job');
            $table->string('c_office_number')->nullable();
            $table->string('c_gender');
            $table->string('c_married');
            // if married
            $table->string('c_sup_name')->nullable();
            $table->string('c_sup_job')->nullable();
            $table->string('c_sup_phone')->nullable();
            $table->string('c_sup_id_number')->nullable();
            // Bank Details
            $table->string('c_bank_account');
            $table->string('c_bank_name');
            $table->string('c_bank_branch');
            // Documents
            $table->string('c_id_copy');
            $table->string('c_id_copy_back');
            $table->string('c_ceb_bill');
            $table->string('c_bank_book');
            $table->string('status')->nullable();
            $table->string('c_banned')->nullable();

            $table->string('c_group');
            $table->string('c_user');

            $table->foreign('c_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('c_group')->references('id')->on('customer_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_details');
    }
}

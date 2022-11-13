<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('l_id');
            $table->foreignId('l_approved');
            $table->foreignId('l_approve_state');
            $table->foreignId('status');
            $table->string('l_comments');

            $table->foreign('l_approved')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('l_id')->references('id')->on('loans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_approvals');
    }
}

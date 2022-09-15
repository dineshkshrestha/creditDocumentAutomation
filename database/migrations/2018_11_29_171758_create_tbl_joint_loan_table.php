<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJointLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_joint_loan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned();
            $table->foreign('borrower_id')->references('id')->on('tbl_joint_borrower')->onUpdate('cascade');
            $table->string('loan_amount');
            $table->string('loan_amount_words');
            $table->integer('offerletter_day');
            $table->integer('offerletter_month');
            $table->integer('offerletter_year');
            $table->integer('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('tbl_branch')->onUpdate('cascade');
            $table->string('approved_by')->nullable();
            $table->string('document_status');
            $table->longText('document_remarks')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_joint_loan');
    }
}

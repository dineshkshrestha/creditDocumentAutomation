<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJointGuarantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_joint_guarantor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned();
            $table->foreign('borrower_id')->references('id')->on('tbl_joint_borrower')->onUpdate('cascade');
            $table->integer('personal_guarantor_id')->nullable()->unsigned();
            $table->foreign('personal_guarantor_id')->references('id')->on('tbl_personal_guarantor')->onUpdate('cascade');
            $table->boolean('status');
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
        Schema::dropIfExists('tbl_joint_guarantor');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJointPropertyOwnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_joint_property_owner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('joint1')->nullable();
            $table->integer('joint2')->nullable();
            $table->integer('joint3')->nullable();
            $table->integer('personal_borrower_id')->unsigned()->nullable();
            $table->foreign('personal_borrower_id')->references('id')->on('tbl_personal_borrower')->onUpdate('cascade');
            $table->integer('corporate_borrower_id')->unsigned()->nullable();
            $table->foreign('corporate_borrower_id')->references('id')->on('tbl_corporate_borrower')->onUpdate('cascade');
            $table->integer('joint_borrower_id')->unsigned()->nullable();
            $table->foreign('joint_borrower_id')->references('id')->on('tbl_joint_borrower')->onUpdate('cascade');
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
        Schema::dropIfExists('tbl_joint_property_owner');
    }
}

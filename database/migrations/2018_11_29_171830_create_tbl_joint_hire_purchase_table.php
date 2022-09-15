<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJointHirePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_joint_hire_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned();
            $table->foreign('borrower_id')->references('id')->on('tbl_joint_borrower')->onUpdate('cascade');
            $table->string('model_number');
            $table->string('registration_number');
            $table->string('engine_number');
            $table->string('chassis_number');
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
        Schema::dropIfExists('tbl_joint_hire_purchase');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDispatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dispatch', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date');
                $table->string('loan_amount_words');
                $table->string('reference_number');
                $table->string('subject');
                $table->string('receiver');
                $table->string('remarks');
                $table->integer('branch')->unsigned();
                $table->foreign('branch')->references('id')->on('tbl_branch')->onUpdate('cascade');
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
        Schema::dropIfExists('tbl_dispatch');
    }
}

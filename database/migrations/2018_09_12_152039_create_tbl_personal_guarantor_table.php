<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPersonalGuarantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_personal_guarantor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone')->nullable();

            $table->string('english_name');
            $table->string('nepali_name');
            $table->string('client_id')->nullable();
            $table->string('grandfather_name');
            $table->string('grandfather_relation');
            $table->string('father_name');
            $table->string('father_relation');
            $table->string('spouse_name')->nullable();
            $table->string('spouse_relation')->nullable();
            $table->integer('district_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('tbl_district')->onUpdate('cascade');
            $table->integer('local_bodies_id')->unsigned();
            $table->foreign('local_bodies_id')->references('id')->on('tbl_local_bodies')->onUpdate('cascade');
            $table->integer('wardno');
            $table->integer('gender');
            $table->string('citizenship_number');
            $table->integer('issued_district');
            $table->integer('issued_day');
            $table->integer('issued_month');
            $table->integer('issued_year');
            $table->integer('dob_year');
            $table->integer('dob_month');
            $table->integer('dob_day');
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
        Schema::dropIfExists('tbl_personal_guarantor');
    }
}

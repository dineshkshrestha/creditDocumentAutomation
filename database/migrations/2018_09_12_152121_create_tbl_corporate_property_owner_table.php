<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCorporatePropertyOwnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_corporate_property_owner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ministry_id')->unsigned();
            $table->foreign('ministry_id')->references('id')->on('tbl_ministry')->onUpdate('cascade');

            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('tbl_department')->onUpdate('cascade');

            $table->string('registration_number');
            $table->string('phone')->nullable();

            $table->integer('district_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('tbl_district')->onUpdate('cascade');
            $table->integer('local_bodies_id')->unsigned();
            $table->foreign('local_bodies_id')->references('id')->on('tbl_local_bodies')->onUpdate('cascade');
            $table->string('wardno');
            $table->string('english_name');
            $table->string('nepali_name');
            $table->string('client_id')->nullable();
            $table->integer('reg_year');
            $table->integer('reg_month');
            $table->integer('reg_day');
            $table->boolean('status');
            $table->integer('authorized_person_id')->unsigned();
            $table->foreign('authorized_person_id')->references('id')->on('tbl_authorized_person')->onUpdate('cascade');
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
        Schema::dropIfExists('tbl_corporate_property_owner');
    }
}

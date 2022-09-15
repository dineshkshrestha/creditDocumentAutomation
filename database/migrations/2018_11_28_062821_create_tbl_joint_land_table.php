<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJointLandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_joint_land', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('joint_id')->unsigned();
            $table->foreign('joint_id')->references('id')->on('tbl_joint_property_owner')->onUpdate('cascade');
            $table->integer('district_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('tbl_district')->onUpdate('cascade');
            $table->integer('local_bodies_id')->unsigned();
            $table->foreign('local_bodies_id')->references('id')->on('tbl_local_bodies')->onUpdate('cascade');
            $table->integer('wardno');
            $table->string('sheet_no')->nullable();
            $table->string('kitta_no');
            $table->string('area');
            $table->string('remarks')->nullable();
            $table->string('malpot');
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
        Schema::dropIfExists('tbl_joint_land');
    }
}

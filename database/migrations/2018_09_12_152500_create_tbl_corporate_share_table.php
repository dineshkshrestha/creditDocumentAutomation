<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCorporateShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_corporate_share', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_owner_id')->unsigned();
            $table->foreign('property_owner_id')->references('id')->on('tbl_corporate_property_owner')->onUpdate('cascade');
            $table->string('client_id')->nullable();
            $table->string('dpid');
            $table->string('kitta');
            $table->integer('isin')->unsigned();
            $table->foreign('isin')->references('id')->on('tbl_registered_company')->onUpdate('cascade');
            $table->string('share_type');
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
        Schema::dropIfExists('tbl_corporate_share');
    }
}

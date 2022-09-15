<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPersonalShareBorrowerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_personal_share_borrower', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned();
            $table->foreign('borrower_id')->references('id')->on('tbl_personal_borrower')->onUpdate('cascade');
            $table->integer('personal_share_id')->nullable()->unsigned();
            $table->foreign('personal_share_id')->references('id')->on('tbl_personal_share')->onUpdate('cascade');
            $table->integer('corporate_share_id')->nullable()->unsigned();
            $table->foreign('corporate_share_id')->references('id')->on('tbl_corporate_share')->onUpdate('cascade');
            $table->string('property_type');
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
        Schema::dropIfExists('tbl_personal-share_borrower');
    }
}

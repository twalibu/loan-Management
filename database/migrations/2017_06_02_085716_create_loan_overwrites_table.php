<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanOverwritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_overwrites', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount', 15, 2);
            $table->date('date');
            $table->integer('schedule_id')->index();
            $table->integer('loan_id')->index();
            $table->integer('tenant_id')->index();
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
        Schema::dropIfExists('loan_overwrites');
    }
}

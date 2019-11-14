<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanPaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_payment_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->double('principal', 15, 2);
            $table->double('interest', 15, 2);
            $table->double('amount', 15, 2);
            $table->double('paid', 15, 2);
            $table->double('balance', 15, 2);
            $table->integer('month');
            $table->string('status');
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
        Schema::dropIfExists('loan_payment_schedules');
    }
}

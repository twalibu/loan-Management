<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->date('alert_one');
            $table->date('alert_two');
            $table->date('alert_three');
            $table->integer('schedule_id');
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
        Schema::dropIfExists('schedule_alerts');
    }
}

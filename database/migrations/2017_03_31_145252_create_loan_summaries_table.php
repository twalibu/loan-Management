<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->double('monthly', 15, 2);
            $table->double('interest', 15, 2);
            $table->double('principal', 15, 2);
            $table->double('penalt', 15, 2);
            $table->double('paid', 15, 2);
            $table->double('overwrite', 15, 2);
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
        Schema::dropIfExists('loan_summaries');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_number_alerts', function (Blueprint $table) {
            $table->id();
            $table->integer('no_of_jobs');
            $table->integer('days');
            $table->string('condition');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('receivers');
            $table->dateTime('last_alert')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_number_alerts');
    }
};

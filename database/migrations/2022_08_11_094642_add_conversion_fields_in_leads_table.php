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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('conversion_value')->nullable();
            $table->string('conversion_currency')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->boolean('uploaded')->default(false);

            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn(['conversion_value', 'conversion_currency', 'job_id', 'uploaded']);
        });
    }
};

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
        Schema::table('flex_calls', function (Blueprint $table) {
            $table->unsignedBigInteger('conference_id')->after('sid')->nullable();
            $table->foreign('conference_id')->references('id')->on('flex_conferences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flex_calls', function (Blueprint $table) {
            $table->dropForeign(['conference_id']);
            $table->dropColumn('conference_id');
        });
    }
};

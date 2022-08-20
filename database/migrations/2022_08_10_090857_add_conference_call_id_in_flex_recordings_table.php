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
        Schema::table('flex_recordings', function (Blueprint $table) {
            $table->unsignedBigInteger('conference_id')->nullable()->after('conference_sid');
            $table->unsignedBigInteger('call_id')->nullable()->after('call_sid');
            $table->foreign('conference_id')->references('id')->on('flex_conferences')->onDelete('cascade');
            $table->foreign('call_id')->references('id')->on('flex_calls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flex_recordings', function (Blueprint $table) {
            $table->dropForeign(['conference_id']);
            $table->dropForeign(['call_id']);
            $table->dropColumn(['conference_id', 'call_id']);
        });
    }
};

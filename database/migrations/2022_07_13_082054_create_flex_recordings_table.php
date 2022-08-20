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
        Schema::create('flex_recordings', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('conference_sid')->nullable();
            $table->string('call_sid')->nullable();
            $table->string('account_sid');
            $table->string('duration');
            $table->text('media_url');
            $table->json('json');
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
        Schema::dropIfExists('flex_recordings');
    }
};

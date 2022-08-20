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
        Schema::create('flex_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->integer('age');
            $table->string('status');
            $table->text('attributes');
            $table->string('queue_name');
            $table->string('channel_name');
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
        Schema::dropIfExists('flex_tasks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flex_recordings', function (Blueprint $table) {
            $table->string('duration')->nullable()->change();
            $table->text('media_url')->nullable()->change();
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
            $table->string('duration')->nullable(false)->change();
            $table->text('media_url')->nullable(false)->change();
        });
    }
};

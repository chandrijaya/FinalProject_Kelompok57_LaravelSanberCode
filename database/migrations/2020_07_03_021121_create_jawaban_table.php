<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();;
            $table->string('isi');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pertanyaan_id');
            $table->boolean('is_selected')->nullable();
            $table->timestamps();
            $table->foreign('pertanyaan_id')->references('id')->on('pertanyaan');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawaban');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_albums', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('albums_id')->unsigned()->nullable(false);
            $table->foreign('albums_id')
            ->references('id')
            ->on('albums')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->bigInteger('medias_id')->unsigned()->nullable(false);
            $table->foreign('medias_id')
            ->references('id')
            ->on('medias')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->bigInteger('users_id')->unsigned()->nullable(false);
            $table->foreign('users_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_albums');
    }
};

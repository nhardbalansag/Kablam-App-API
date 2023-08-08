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
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('media_title');
            $table->string('media_description');
            $table->string('user_calendar_premiere_id')->nullable(true);
            $table->boolean('is_active')->default(true)->nullable(false);;

            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->foreign('user_id')
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
        Schema::dropIfExists('medias');
    }
};

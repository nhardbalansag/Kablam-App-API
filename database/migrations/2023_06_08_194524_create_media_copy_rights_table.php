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
        Schema::create('media_copy_rights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('copyright_media_id')->nullable(false);
            $table->string('copyright_owner_information')->nullable(false);

            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->bigInteger('media_id')->unsigned()->nullable(false);
            $table->foreign('media_id')
            ->references('id')
            ->on('medias')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->string('status')->default('active')->nullable(false); // active|pending|decline

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_copy_rights');
    }
};

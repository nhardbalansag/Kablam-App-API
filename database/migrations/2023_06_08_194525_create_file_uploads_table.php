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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('origin_name');
            $table->integer('size');
            $table->integer('duration')->nullable(true);
            $table->string('extension');
            $table->string('mime_type');
            $table->string('url');
            $table->string('upload_type'); // thumbnail|video|audio|copyright|proofID
            $table->boolean('is_active')->default(true)->nullable(false);

            $table->bigInteger('medias_id')->unsigned();
            $table->foreign('medias_id')
            ->references('id')
            ->on('medias')
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
        Schema::dropIfExists('file_uploads');
    }
};

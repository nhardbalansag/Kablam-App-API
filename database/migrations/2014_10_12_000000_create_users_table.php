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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birthday')->nullable();
            $table->string('number')->nullable();
            $table->boolean('is_verified')->default(false);

            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')
             ->references('id')
             ->on('roles')
             ->onDelete('cascade')
             ->onUpdate('cascade');

            $table->string('user_photo')->nullable();
            $table->boolean('is_active')->default(true);

            $table->string('login_type')->nullable(false);

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id(); // Автоінкрементний primary key
            $table->string('name'); // Ім'я користувача
            $table->string('email')->unique(); // Унікальний email
            $table->string('password'); // Пароль
            $table->timestamps(); // created_at та updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('test');
    }

};

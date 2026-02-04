<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('director_id')->constrained('directors')->onDelete('cascade');
            $table->string('bank_name');
            $table->string('card_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('director_id')->constrained('directors')->onDelete('cascade');
            $table->foreignId('credit_card_id')->constrained('credit_cards')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('credit_limit', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_reports');
    }
};
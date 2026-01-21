<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('monthly_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('director_id')->constrained('directors')->onDelete('cascade');
        $table->integer('month');
        $table->integer('year');
        $table->decimal('credit_limit', 15, 2);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
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
    Schema::create('monthly_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('director_id')->constrained('directors')->onDelete('cascade');
        $table->integer('month');       // Bulan (1-12)
        $table->integer('year');        // Tahun (2025, 2026)
        $table->decimal('credit_limit', 15, 2); // Pagu / Batas Kredit (Uang Awal)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};

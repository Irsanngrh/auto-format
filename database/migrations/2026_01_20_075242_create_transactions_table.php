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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        // Menghubungkan transaksi ke laporan bulan tertentu
        $table->foreignId('monthly_report_id')->constrained('monthly_reports')->onDelete('cascade');
        $table->date('transaction_date'); // Tanggal Transaksi
        $table->string('description');    // Keterangan
        $table->decimal('amount', 15, 2); // Jumlah Uang Keluar
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->string('nama_dokter');
            $table->string('spesialisasi');
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->string('price')->default('Rp 180.000');
            $table->decimal('price_num', 12, 2)->default(180000.00);
            $table->string('initials', 10)->nullable();
            $table->string('image')->nullable();
            $table->string('availability')->default('9AM - 8PM');
            $table->string('experience')->default('10 years');
            $table->string('patients')->default('3.8k Up');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

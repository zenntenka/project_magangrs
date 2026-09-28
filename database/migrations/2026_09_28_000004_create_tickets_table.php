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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang')->index();
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditutup'])->default('baru')->index();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lampiran')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamps();

            // Index tambahan untuk efisiensi filter dashboard dan laporan
            $table->index(['status', 'prioritas']);
            $table->index(['user_id', 'status']);
            $table->index(['teknisi_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

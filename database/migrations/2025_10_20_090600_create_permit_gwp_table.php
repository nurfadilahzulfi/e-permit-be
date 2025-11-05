<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitGwpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permit_gwp', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel permit_types
            $table->foreignId('permit_type_id')->constrained('permit_types')->onDelete('cascade');
            $table->string('nomor')->unique();
            $table->dateTime('tgl_permohonan');
            $table->string('shift_kerja', 20);
            $table->string('lokasi', 255);
            $table->text('deskripsi_pekerjaan');
            $table->text('peralatan_pekerjaan');

            // --- INI SUDAH BENAR ---
            // $table->integer('pemohon_id'); // Tidak perlu, karena kita pakai foreign di bawah
            $table->enum('pemohon_jenis', ['internal', 'eksternal'])->default('internal');
            $table->integer('status')->default(0);

            // --- INI PERUBAHANNYA ---

            // 1. Mengganti 'pemilik_lokasi_jenis' dengan 'supervisor_id'
            // Kita gunakan nama tabel 'user' sesuai file migrasi pertama kamu
            $table->foreignId('supervisor_id')->constrained('user')->onDelete('cascade');

            // 2. Menambahkan 'valid_until' untuk masa berlaku 2 minggu
            $table->timestamp('valid_until')->nullable();

            $table->timestamps();

            // Index dan foreign key tambahan (pemohon_id)
            // Kita ubah integer('pemohon_id') menjadi foreignId() agar lebih rapi
            $table->foreignId('pemohon_id')->constrained('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permit_gwp');
    }
}

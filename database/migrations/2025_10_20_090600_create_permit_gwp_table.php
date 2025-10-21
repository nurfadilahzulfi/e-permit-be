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
            $table->integer('pemohon_id');
            $table->enum('pemohon_jenis', ['internal', 'eksternal'])->default('internal');
            $table->integer('pemilik_lokasi_jenis');
            $table->integer('status')->default(0);
            $table->timestamps();

            // Index dan foreign key tambahan
            $table->foreign('pemohon_id')->references('id')->on('users')->onDelete('cascade');
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

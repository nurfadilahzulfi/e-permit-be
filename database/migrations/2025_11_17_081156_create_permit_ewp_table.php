<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitEwpTable extends Migration
{
    public function up()
    {
        Schema::create('permit_ewp', function (Blueprint $table) {
            $table->id();

            // Kunci Induk (Wajib ada)
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');
            $table->foreignId('permit_type_id')->constrained('permit_types')->onDelete('cascade');
            $table->string('permit_type_kode', 20)->nullable(); // Misal: EWP

            // --- Kolom Spesifik EWP (dari Form PDF) ---
            $table->string('kedalaman_galian_meter')->nullable();
            $table->string('tipe_tanah')->nullable();
            $table->boolean('utility_bawah_tanah_teridentifikasi')->default(false);
            $table->text('catatan_utility')->nullable(); // Penjelasan utility apa
            $table->boolean('dokumen_pendukung_jsa')->default(false);
            $table->boolean('dokumen_pendukung_gambar_teknik')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permit_ewp');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPermitCompletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel ini akan menyimpan log 'tanda tangan' penutupan
        // (1 baris untuk Pemohon, 1 baris untuk HSE, 1 baris untuk SPV)
        Schema::create('work_permit_completions', function (Blueprint $table) {
            $table->id();

            // Relasi ke Izin Kerja Induk
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');

            // Siapa yang tanda tangan
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');

                                                  // Peran saat tanda tangan
            $table->string('role_penutupan', 50); // 'pemohon', 'hse', 'supervisor'

            $table->timestamp('tgl_penutupan');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_permit_completions');
    }
}

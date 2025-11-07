<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel ini akan mencatat log 3-langkah penutupan
        Schema::create('work_permit_completions', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Induk" Izin Kerja
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');

            // Siapa yang harus menandatangani
            $table->foreignId('user_id')->constrained('user');
            $table->string('role_penutupan', 50); // hse, supervisor, pemohon

                                       // Urutan tanda tangan
            $table->integer('urutan'); // 1=HSE, 2=Supervisor, 3=Pemohon

                                                             // Status tanda tangan
            $table->integer('status_penutupan')->default(0); // 0=Pending, 1=Signed
            $table->dateTime('tgl_penutupan')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_permit_completions');
    }
};

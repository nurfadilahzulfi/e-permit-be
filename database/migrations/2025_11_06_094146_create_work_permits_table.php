<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel "Induk" untuk semua Izin Kerja
        Schema::create('work_permits', function (Blueprint $table) {
            $table->id();

                                                         // Info Pekerjaan (Diisi oleh HSE)
            $table->string('nomor_pekerjaan')->unique(); // Nomor baru untuk pekerjaan
            $table->text('deskripsi_pekerjaan');
            $table->string('lokasi');
            $table->string('shift_kerja', 20);

            // Relasi ke User
            $table->foreignId('pemohon_id')->constrained('user');
            $table->foreignId('supervisor_id')->constrained('user');
            $table->foreignId('hse_id')->constrained('user'); // User HSE yang menginisiasi

            // Status Izin Kerja (Induk)
            // 0=Draft, 1=Pending Checklist, 2=Pending Approval, 3=Approved, 4=Rejected, 5=Closed
            $table->integer('status')->default(0);

            $table->dateTime('tgl_pekerjaan_dimulai');
            $table->dateTime('tgl_pekerjaan_selesai');
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
        Schema::dropIfExists('work_permits');
    }
};

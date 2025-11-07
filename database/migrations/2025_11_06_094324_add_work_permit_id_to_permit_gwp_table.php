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
        Schema::table('permit_gwp', function (Blueprint $table) {
            // 1. Tambahkan kolom foreign key baru ke "Induk"
            $table->foreignId('work_permit_id')
                ->after('id')
                ->constrained('work_permits')
                ->onDelete('cascade');

            // 2. Hapus kolom-kolom duplikat yang sudah ada di "Induk"
            // (Kita akan buat ini 'nullable' dulu dan hapus belakangan agar data lama aman)
            // Untuk proyek baru, kita bisa langsung hapus:
            $table->dropForeign(['pemohon_id']);
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['pemohon_id', 'supervisor_id', 'nomor', 'tgl_permohonan', 'shift_kerja', 'lokasi', 'deskripsi_pekerjaan', 'status']);

            // Kolom 'peralatan_pekerjaan' mungkin masih spesifik untuk GWP, jadi kita biarkan.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permit_gwp', function (Blueprint $table) {
            // (Tambahkan logika rollback jika diperlukan)
            $table->dropForeign(['work_permit_id']);
            $table->dropColumn('work_permit_id');

            // Tambahkan kembali kolom yang dihapus
            $table->foreignId('pemohon_id')->constrained('user');
            $table->foreignId('supervisor_id')->constrained('user');
            $table->string('nomor')->unique();
            $table->dateTime('tgl_permohonan');
            $table->string('shift_kerja', 20);
            $table->string('lokasi', 255);
            $table->text('deskripsi_pekerjaan');
            $table->integer('status')->default(0);
        });
    }
};

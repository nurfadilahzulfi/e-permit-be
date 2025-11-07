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
        // 1. Hapus foreign key LAMA dulu (saat nama tabel masih 'permit_gwp_approval')
        Schema::table('permit_gwp_approval', function (Blueprint $table) {

            // [PERBAIKAN] Kita panggil nama constraint-nya secara eksplisit
            $table->dropForeign('permit_gwp_approval_permit_gwp_id_foreign');

            // Ubah nama kolomnya
            $table->renameColumn('permit_gwp_id', 'work_permit_id');
        });

        // 2. Ubah nama tabel (SETELAH foreign key di-drop)
        Schema::rename('permit_gwp_approval', 'work_permit_approvals');

        // 3. Tambahkan foreign key BARU (di tabel yang sudah diganti namanya)
        Schema::table('work_permit_approvals', function (Blueprint $table) {
            $table->foreign('work_permit_id')
                ->references('id')
                ->on('work_permits')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Hapus foreign key baru
        Schema::table('work_permit_approvals', function (Blueprint $table) {
            $table->dropForeign(['work_permit_id']);
        });

        // 2. Ubah nama tabel kembali
        Schema::rename('work_permit_approvals', 'permit_gwp_approval');

        // 3. Ubah nama kolom & tambahkan foreign key lama
        Schema::table('permit_gwp_approval', function (Blueprint $table) {
            $table->renameColumn('work_permit_id', 'permit_gwp_id');

            $table->foreign('permit_gwp_id')
                ->references('id')
                ->on('permit_gwp')
                ->onDelete('cascade');
        });
    }
};

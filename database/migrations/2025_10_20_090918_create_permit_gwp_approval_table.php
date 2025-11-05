<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitGwpApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permit_gwp_approval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permit_gwp_id')->constrained('permit_gwp')->onDelete('cascade');

            // --- INI PERUBAHANNYA ---

            // 1. Mengubah 'approver_id' menjadi foreign key ke tabel 'user'
            $table->foreignId('approver_id')->constrained('user');

            // 2. Mengubah 'tgl_persetujuan' agar bisa NULL
            $table->dateTime('tgl_persetujuan')->nullable();

            // --- INI SUDAH BENAR ---
            $table->string('role_persetujuan', 50);
            $table->integer('status_persetujuan')->default(0);
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
        Schema::dropIfExists('permit_gwp_approval');
    }
}

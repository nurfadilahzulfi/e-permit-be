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
            $table->foreignId('approver_id')->constrained('user');
            $table->dateTime('tgl_persetujuan')->nullable();
            $table->string('role_persetujuan', 50);

                                                   // V V V TAMBAHKAN BARIS INI V V V
            $table->integer('urutan')->default(1); // Untuk menentukan urutan persetujuan (SPV=1, HSE=2)
                                                   // ^ ^ ^ TAMBAHKAN BARIS INI ^ ^ ^

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

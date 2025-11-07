<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJsaColumnsToWorkPermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_permits', function (Blueprint $table) {
            // Tambahkan 3 kolom JSA setelah 'deskripsi_pekerjaan'
            $table->text('langkah_pekerjaan')->nullable()->after('deskripsi_pekerjaan');
            $table->text('potensi_bahaya')->nullable()->after('langkah_pekerjaan');
            $table->text('pengendalian_risiko')->nullable()->after('potensi_bahaya');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_permits', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['langkah_pekerjaan', 'potensi_bahaya', 'pengendalian_risiko']);
        });
    }
}

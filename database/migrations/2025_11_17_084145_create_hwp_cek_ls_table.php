<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHwpCekLsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel master untuk checklist Hot Work Permit
        Schema::create('hwp_cek_ls', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            // $table->string('tipe', 50)->default('hse'); // Opsional jika mau dikelompokkan
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
        Schema::dropIfExists('hwp_cek_ls');
    }
}

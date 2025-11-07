<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCseCekGasLsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // BENAR
    public function up()
    {
        Schema::create('cse_cek_gas_ls', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // atau kolom lain sesuai kebutuhan Anda
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
        Schema::dropIfExists('cse_cek_gas_ls');
    }
}

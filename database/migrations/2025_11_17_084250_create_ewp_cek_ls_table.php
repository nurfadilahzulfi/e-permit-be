<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEwpCekLsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel master untuk checklist Excavation Work Permit
        Schema::create('ewp_cek_ls', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
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
        Schema::dropIfExists('ewp_cek_ls');
    }
}

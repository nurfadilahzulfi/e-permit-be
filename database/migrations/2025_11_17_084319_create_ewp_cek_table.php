<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEwpCekTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel untuk menyimpan jawaban checklist EWP
        Schema::create('ewp_cek', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Anak" Permit EWP
            $table->foreignId('permit_ewp_id')->constrained('permit_ewp')->onDelete('cascade');

            // Kolom Polymorphic
            $table->string('model', 100);
            $table->unsignedBigInteger('ls_id');

            $table->boolean('value')->default(false);
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
        Schema::dropIfExists('ewp_cek');
    }
}

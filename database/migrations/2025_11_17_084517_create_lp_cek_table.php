<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpCekTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel untuk menyimpan jawaban checklist LP
        Schema::create('lp_cek', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Anak" Permit LP
            $table->foreignId('permit_lp_id')->constrained('permit_lp')->onDelete('cascade');

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
        Schema::dropIfExists('lp_cek');
    }
}

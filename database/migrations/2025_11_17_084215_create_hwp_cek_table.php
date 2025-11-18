<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHwpCekTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel untuk menyimpan jawaban checklist HWP
        Schema::create('hwp_cek', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Anak" Permit HWP
            $table->foreignId('permit_hwp_id')->constrained('permit_hwp')->onDelete('cascade');

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
        Schema::dropIfExists('hwp_cek');
    }
}

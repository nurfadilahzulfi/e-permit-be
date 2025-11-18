<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGwpCekTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gwp_cek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permit_gwp_id')->constrained('permit_gwp')->onDelete('cascade');

            // --- KEMBALIKAN KE KODE ASLI ANDA ---
            $table->string('model', 100);
            $table->unsignedBigInteger('ls_id');
            // --- SELESAI PERUBAHAN ---

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
        Schema::dropIfExists('gwp_cek');
    }
}

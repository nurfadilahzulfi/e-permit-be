<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitLpTable extends Migration
{
    public function up()
    {
        Schema::create('permit_lp', function (Blueprint $table) {
            $table->id();

            // Kunci Induk (Wajib ada)
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');
            $table->foreignId('permit_type_id')->constrained('permit_types')->onDelete('cascade');
            $table->string('permit_type_kode', 20)->nullable(); // Misal: LP

            // --- Kolom Spesifik Lifting Permit (dari Form PDF) ---
            $table->string('crane_capacity', 100)->nullable();
            $table->string('crane_type', 100)->nullable();
            $table->string('load_weight', 100)->nullable();
            $table->string('load_dimension', 100)->nullable();
            $table->string('sling_angle', 100)->nullable();
            $table->integer('sling_quantity')->nullable();
            $table->string('shackle_capacity', 100)->nullable();
            $table->string('spreader_beam_capacity', 100)->nullable();
            // (Tambahkan semua kolom spesifik dari form PDF...)

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permit_lp');
    }
}

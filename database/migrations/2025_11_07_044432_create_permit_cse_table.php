<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permit_cse', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Induk" Izin Kerja
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');

            // Relasi ke Master Jenis Izin
            $table->foreignId('permit_type_id')->constrained('permit_types')->onDelete('cascade');
            $table->string('permit_type_kode'); // misal: "CSE"

            // Kolom spesifik untuk CSE
            $table->string('gas_tester_name')->nullable();
            $table->string('entry_supervisor_name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permit_cse');
    }
};

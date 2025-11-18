<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitHwpTable extends Migration
{
    public function up()
    {
        Schema::create('permit_hwp', function (Blueprint $table) {
            $table->id();

            // Kunci Induk (Wajib ada, menghubungkan ke work_permits)
            $table->foreignId('work_permit_id')->constrained('work_permits')->onDelete('cascade');

            // Kunci Tipe Permit (Wajib ada)
            $table->foreignId('permit_type_id')->constrained('permit_types')->onDelete('cascade');
            $table->string('permit_type_kode', 20)->nullable(); // Misal: HWP

                                                         // --- Kolom Spesifik HWP (dari Form PDF) ---
                                                         // Karena form HWP di PDF sangat fokus di checklist,
                                                         // kita bisa tambahkan kolom spesifik jika ada,
                                                         // atau biarkan kosong dan fokus di checklist-nya.
                                                         // Contoh:
            $table->text('equipment_tools')->nullable(); // Equipment/Tools yang Digunakan

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permit_hwp');
    }
}

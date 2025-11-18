<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_permits', function (Blueprint $table) {
            // [PERBAIKAN]
            // Buat Indeks Gabungan untuk 'status' DAN 'created_at'
            // Ini akan mempercepat query:
            // ->where('status', 10)->latest()
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_permits', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
// [BARU] Pastikan Anda menambahkan 'use' statement ini
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // [PERBAIKAN] Gunakan SQL mentah untuk PostgreSQL agar kolom menjadi NULLABLE
        DB::statement('ALTER TABLE work_permits ALTER COLUMN hse_id DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // [PERBAIKAN] Kembalikan ke NOT NULL jika di-rollback
        DB::statement('ALTER TABLE work_permits ALTER COLUMN hse_id SET NOT NULL');
    }
};

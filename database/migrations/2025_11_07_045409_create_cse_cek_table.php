<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cse_cek', function (Blueprint $table) {
            $table->id();

            // Relasi ke "Anak" Permit CSE
            $table->foreignId('permit_cse_id')->constrained('permit_cse')->onDelete('cascade');

            // Relasi Morph (Polimorfik) ke Master Checklist
            $table->string('model', 100);
            $table->unsignedBigInteger('ls_id');

            $table->boolean('value')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cse_cek');
    }
};

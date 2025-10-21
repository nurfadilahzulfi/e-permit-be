<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitGwpCompletionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permit_gwp_completion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permit_gwp_id')->constrained('permit_gwp')->onDelete('cascade');
            $table->integer('completion_id');
            $table->string('role_completion', 50);
            $table->integer('status_completion')->default(0);
            $table->dateTime('tgl_completion');
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('permit_gwp_completion');
    }
}

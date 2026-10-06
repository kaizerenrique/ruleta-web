<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->string('nick', 50)->index();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->foreignId('premio_id')->nullable()->constrained('premios')->onDelete('set null');
            $table->string('premio_nombre', 100)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};

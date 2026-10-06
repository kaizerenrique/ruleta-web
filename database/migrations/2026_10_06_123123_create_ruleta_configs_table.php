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
        Schema::create('ruleta_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('max_giros_por_usuario')->default(1);
            $table->boolean('captcha_activo')->default(true);
            $table->timestamps();
        });

        DB::table('ruleta_configs')->insert([
            'max_giros_por_usuario' => 1,
            'captcha_activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruleta_configs');
    }
};

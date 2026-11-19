<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->boolean('es_premio')->nullable();
        });

        DB::table('resultados')
            ->whereIn('premio_id', DB::table('premios')->select('id')->where('es_premio', true))
            ->update(['es_premio' => true]);

        DB::table('resultados')
            ->whereIn('premio_id', DB::table('premios')->select('id')->where('es_premio', false))
            ->update(['es_premio' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropColumn('es_premio');
        });
    }
};

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
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropIndex('resultados_nick_index');
            $table->index(['nick', 'fecha']);
            $table->index(['ip', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropIndex(['nick', 'fecha']);
            $table->dropIndex(['ip', 'fecha']);
            $table->index('nick');
        });
    }
};

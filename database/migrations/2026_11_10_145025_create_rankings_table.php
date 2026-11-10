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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->string('tour', 3);
            // Data em que a Tennis API publicou o ranking. Guardar as semanas
            // anteriores permite comparar posições sem custo de requisição.
            $table->date('ranked_at');
            $table->unsignedSmallInteger('position');
            $table->unsignedBigInteger('player_id');
            $table->string('player_name');
            $table->string('country_name')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->unsignedInteger('points');

            $table->text('photo_url')->nullable();
            $table->timestamps();
            $table->unique(['tour', 'ranked_at', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};

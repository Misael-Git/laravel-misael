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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        
        // 1. Relación con el usuario: Crea la columna user_id y la vincula
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // 2. Campos de tu entidad (ejemplo para una lista de tareas)
        $table->string('title');
        $table->text('description')->nullable();
        $table->boolean('is_completed')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

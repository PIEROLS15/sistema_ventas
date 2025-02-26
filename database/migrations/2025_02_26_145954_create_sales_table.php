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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('cliente_nombre');
            $table->foreignId('identificacion_id')->constrained('identificaciones');
            $table->string('numero_identificacion');
            $table->string('cliente_correo')->nullable();
            $table->foreignId('vendedor_id')->constrained('users');
            $table->decimal('monto_total', 10, 2);
            $table->timestamp('fecha_venta')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('supplier_name');
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['servicios', 'nomina', 'honorarios_medicos', 'insumos', 'equipo', 'otros']);
            $table->text('description');
            $table->date('expense_date');
            $table->enum('payment_method', ['efectivo', 'transferencia', 'cheque']);
            $table->uuid('registered_by');
            $table->timestamps();

            $table->foreign('registered_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

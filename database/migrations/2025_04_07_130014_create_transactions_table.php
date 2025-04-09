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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->string('type')->comment('income or expense');
            $table->string('transaction_title')->nullable();
            $table->string('description')->nullable();
            $table->string('counterparty')->nullable();
            $table->date('transaction_date');
            $table->string('source')->nullable();
            $table->string('reference_id')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

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
        Schema::create('mapping_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('skip_rows')->default(0);
            $table->string('transaction_title');
            $table->string('description');
            $table->string('counterparty');
            $table->string('location');
            $table->string('transaction_date');
            $table->string('amount');
            $table->string('type');
            $table->string('reference_id');
            $table->string('card_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_profiles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['BUY', 'SELL']);
            $table->decimal('price', 16, 2);
            $table->string('provider')->nullable();
            $table->date('price_date');
            $table->timestamps();

            $table->index(['type', 'price_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};

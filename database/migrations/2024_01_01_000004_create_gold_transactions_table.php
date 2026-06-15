<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('family_members');
            $table->enum('type', ['BUY', 'SELL', 'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT']);
            $table->decimal('gram', 16, 4);
            $table->decimal('price_per_gram', 16, 2);
            $table->decimal('total_amount', 16, 2);
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('type');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_transactions');
    }
};

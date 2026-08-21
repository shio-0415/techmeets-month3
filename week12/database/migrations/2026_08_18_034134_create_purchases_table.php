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
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->unsignedBigInteger('amount'); // 最小通貨単位
        $table->string('currency', 10)->default('jpy');
        $table->string('status')->default('pending'); // pending / paid / failed
        $table->string('stripe_session_id')->unique()->nullable();
        $table->string('stripe_payment_intent')->nullable()->index();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

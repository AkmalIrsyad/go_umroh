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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('umrah_package_id')->constrained()->cascadeOnDelete();
            $table->decimal('total', 15, 2);
            $table->integer('kode_unik')->default(0);
            $table->enum('status', ['waiting_payment', 'waiting_confirmation', 'complete'])->default('waiting_payment');
            $table->unsignedBigInteger('travel_agent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

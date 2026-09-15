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
        Schema::create('umrah_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['regular', 'vip', 'promo'])->default('regular');
            $table->date('departure_date');
            $table->decimal('price', 15, 2);
            $table->decimal('booking_fee', 15, 2)->default(0);
            $table->integer('duration');
            $table->integer('capacity');
            $table->integer('booked_count')->default(0);
            $table->text('details')->nullable();
            $table->text('requirement')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->foreignId('air_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('Merchant ID')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umrah_packages');
    }
};

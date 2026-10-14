<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('joki_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();
            $table->string('game', 50)->default('Mobile Legends');
            $table->string('service_type', 30);
            $table->string('player_nickname', 60);
            $table->string('player_id', 20);
            $table->string('zone_id', 10);
            $table->string('current_rank', 30);
            $table->string('target_rank', 30);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('quoted_price')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('joki_orders');
    }
};

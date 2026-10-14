<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_up_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();
            $table->string('game', 50)->default('Mobile Legends');
            $table->string('player_id', 20);
            $table->string('zone_id', 10);
            $table->string('package_key', 30);
            $table->string('package_name', 50);
            $table->unsignedInteger('diamonds');
            $table->unsignedBigInteger('price');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_up_orders');
    }
};

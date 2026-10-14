<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->foreignId('worker_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->index(['worker_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
            $table->dropIndex(['worker_id', 'status']);
            $table->dropColumn('worker_id');
        });
    }
};

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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('quantity')->after('reason');
            $table->decimal('cost_price', 8, 2)->after('quantity');
            $table->foreignId('to_store_id')->after('cost_price')->nullable()->constrained('stores')->onDelete('set null');
            $table->foreignId('from_store_id')->after('to_store_id')->nullable()->constrained('stores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['to_store_id']);
            $table->dropForeign(['from_store_id']);
            $table->dropColumn('to_store_id');
            $table->dropColumn('from_store_id');
            $table->dropColumn('cost_price');
            $table->dropColumn('quantity');
        });
    }
};



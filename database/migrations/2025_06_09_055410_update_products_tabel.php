<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_detail_id')->nullable()->constrained('product_details')->onDelete('set null');

            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
            $table->dropForeign(['product_type_id']);
            $table->dropColumn('product_type_id');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');

            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('barcode');
            $table->dropColumn('image');
            $table->dropColumn('type');
            $table->dropColumn('price');
            $table->dropColumn('status');
        });
    }
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_detail_id']);
            $table->dropColumn('product_detail_id');

            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('barcode')->nullable();
            $table->string('image')->nullable();
            $table->enum('type', ['product', 'service'])->default('product');
            $table->decimal('price', 8, 2);
            $table->boolean('status')->default(true);
        });
    }
};

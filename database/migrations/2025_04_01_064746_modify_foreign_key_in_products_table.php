<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('product_type_id')->nullable()->change();
            $table->unsignedBigInteger('brand_id')->nullable()->change();
            // Drop the existing foreign key constraint
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_type_id']);
            $table->dropForeign(['brand_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            // Add the foreign key constraint back without ON DELETE CASCADE
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null'); // Use 'restrict' or 'set null' as needed
            $table->foreign('product_type_id')
                  ->references('id')->on('product_types')
                  ->onDelete('set null'); // Use 'restrict' or 'set null' as needed
            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->onDelete('set null'); // Use 'restrict' or 'set null' as needed
        });

    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint in the down method
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_type_id']);
            $table->dropForeign(['brand_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            // Re-add the foreign key constraint with ON DELETE CASCADE in the down method
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade'); // Use 'restrict' or 'set null' as needed
            $table->foreign('product_type_id')
                  ->references('id')->on('product_types')
                  ->onDelete('set null'); // Use 'restrict' or 'set null' as needed
            $table->foreign('cascade')
                  ->references('id')->on('brands')
                  ->onDelete('cascade'); // Use 'restrict' or 'set null' as needed

        });
    }
};

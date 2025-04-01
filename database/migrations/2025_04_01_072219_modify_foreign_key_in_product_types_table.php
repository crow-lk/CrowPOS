<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_types', function (Blueprint $table) {
            // Make the category_id column nullable if it is not already
            $table->unsignedBigInteger('category_id')->nullable()->change();

            // Drop the existing foreign key constraint
            $table->dropForeign(['category_id']);
        });

        Schema::table('product_types', function (Blueprint $table) {
            // Add the foreign key constraint back with ON DELETE SET NULL
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null'); // Use 'set null' as needed
        });
    }

    public function down()
    {
        Schema::table('product_types', function (Blueprint $table) {
            // Drop the foreign key constraint in the down method
            $table->dropForeign(['category_id']);
        });

        Schema::table('product_types', function (Blueprint $table) {
            // Re-add the foreign key constraint with ON DELETE CASCADE in the down method
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');
        });
    }
};

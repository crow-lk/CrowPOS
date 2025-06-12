<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        // Insert store and retrieve its ID
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'GRS Main',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into another table using the store ID
        // Replace 'other_table' with your actual table name and adjust the columns accordingly
        DB::table('products')->update([
            'store_id' => $storeId,
            // Add other columns as needed for this table
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->update([
            'store_id' => $storeId,
            // Add other columns as needed for this table
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('orders')->update([
            'store_id' => $storeId,
            // Add other columns as needed for this table
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}




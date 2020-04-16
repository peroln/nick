<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       try{
           Excel::import(new ProductsImport, storage_path('app/public/products.xlsx'));
       } catch(Throwable $e){
           $this->command->error($e->getMessage());
           Log::error($e->getMessage());
           exit;
       }
    }
}

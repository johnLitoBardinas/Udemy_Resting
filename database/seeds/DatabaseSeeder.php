<?php

use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::flushEventListeners();         
         Category::flushEventListeners();         
         Product::flushEventListeners();         
         Transaction::flushEventListeners();
         // 3:33 PM 01-30-2019 { for keep purpose }

    	DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // $this->call(UsersTableSeeder::class);
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        $usersQnty = 1000;
        $categoriesQnty = 30;
        $productssQnty = 1000;
        $transactionsQnty = 1000;

        factory(User::class, $usersQnty)->create();
        factory(Category::class, $categoriesQnty)->create();
        factory(Product::class, $productssQnty)->create()->each(
        	function ($product)
        	{	
        		$categories = Category::all()->random(mt_rand(1, 5))->pluck('id');
        		$product->categories()->attach($categories);
        	}
        );
        factory(Transaction::class, $transactionsQnty)->create();
    }
}

<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*
	Buyers BuyerSellerController
*/
Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);
// getting the 1 buyer and  all of the transaction -> product -> categories
Route::resource('buyers.categories', 'Buyer\BuyerCategoryController', ['only' => ['index']]);

// getting the 1 buyer, and the list of every product transaction sellers 
Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);

// getting the 1 buyer, transactions
Route::resource('buyers.transactions', 'Buyer\BuyerTransactionController', ['only' => ['index']]);  

// getting the 1 buyer, products
Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]); 



/*
	Categories
*/
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);

// using 1 category return all the buyers associating on it
Route::resource('categories.buyers', 'Category\CategoryBuyerController', ['only' => ['index']]);

// allowed to retrieve all the products of a particular categories {id}
Route::resource('categories.products', 'Category\CategoryProductController', ['only' => ['index']]);

// using 1 categories return all its product -> seller
Route::resource('categories.sellers', 'Category\CategorySellerController', ['only' => ['index']]);

// using 1 category return all the products -> with its transaction
Route::resource('categories.transactions', 'Category\CategoryTransactionController', ['only' => ['index']]);



/*
	Products 
*/
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);

// using 1 Product return all the related buyers
Route::resource('products.buyers ', 'Product\ProductBuyerController', ['only' => ['index']]);

// using 1 Product can show all product categories, update the product categories, delete the product categories
Route::resource('products.categories', 'Product\ProductCategoryController', ['only' => ['index', 'update', 'destroy']]);

// using 1 Product return all its related transactions
Route::resource('products.transactions', 'Product\ProductTransactionController', ['only' => ['index']]);

// 1 Buyer 
Route::resource('products.buyers.transactions', 'Product\ProductBuyerTransactionController', ['only' => ['store']]);









/*
	Sellers
*/
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);

// 1 seller return all of its products -> transactions -> buyers
Route::resource('sellers.buyers', 'Seller\SellerBuyerController', ['only' => ['index']]);

// 1 seller return all of its products categories
Route::resource('sellers.categories', 'Seller\SellerCategoryController', ['only' => ['index']]);

// 1 seller return all of its transactions
Route::resource('sellers.transactions', 'Seller\SellerTransactionController', ['only' => ['index']]);

// 1 Seller return all the product related on his/her
Route::resource('sellers.products', 'Seller\SellerProductController', ['except' => ['create', 'edit', 'show']]);




/*
	Transactions 
*/
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);

// getting a 1 transactions and its product categories
Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);

// getting a 1 transaction and its seller data
Route::resource('transactions.sellers', 'Transaction\TransactionSellerController', ['only' => ['index']]); 



/*
	Users
*/
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);

/* 2:13 PM 01-30-2019 { User verification procedure using the verification_token } */
Route::get('users/verify/{token}', 'User\UserController@verify')->name('verify'); // be carefull of the spaces
// always use a plural noun base endpoint 

/* 4:30 PM 01-30-2019 { User resend email verification } */
Route::get('users/{user}/resend', 'User\UserController@resend')->name('resend');


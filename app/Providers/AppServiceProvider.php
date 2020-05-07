<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // defining the default string
        Schema::defaultStringLength(191);

        // 2:53 PM 01-30-2019 { Define some events when the user successfully created }
        User::created(function ($user)
        {
            /*if (!$user->verified) { // if the user is not verified
                Mail::to($user->email)->send(new UserCreated($user));
            }*/
            //retry( 3 parameter [times_to_retryAction, callbackToRetry, numOfMiliSecondsTo_Retry] )
            
            retry(5, function() use ($user)
            {
                Mail::to($user->email)->send(new UserCreated($user));
            }, 100); // 5 times, toSendEmail, within 100 miliseconds
            // in the 5 times within 100 miliseconds the function must call its user send callback
            // if still it does not fire up accordingly then a exception will be thrown

        });

        // 4:17 PM 01-30-2019 { Define a event handling when the specific user email change }
        User::updated(function ($user)
        {
            if ($user->isDirty('email')) { // specifying if the email field was change
                retry(5, function() use ($user)
                {
                    Mail::to($user)->send(new UserMailChanged($user));            
                }, 100);

            }
        });

        // listen to the product update event
        Product::updated(function ($product)
        {
            if ($product->quantity == 0 && $product->isAvailable()) {
                
                // changing the product property
                $product->status = Product::UNAVAILABLE_PRODUCT;

                // saving the new properties of the current product
                $product->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

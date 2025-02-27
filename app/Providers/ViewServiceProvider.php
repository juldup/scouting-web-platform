<?php
 
namespace App\Providers;

use App\ViewComposers\MenuComposer;
use App\ViewComposers\UserBoxComposer;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
 
class ViewServiceProvider extends ServiceProvider {
    
    /**
     * Register any application services.
     */
    public function register(): void {
        // ...
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Using class based composers...
        Facades\View::composer('menu.menu', MenuComposer::class);
        Facades\View::composer('user_box', UserBoxComposer::class);
        
        // Using closure based composers...
        /*Facades\View::composer('user_box', function (View $view) {
            // ...
        });*/
    }
}
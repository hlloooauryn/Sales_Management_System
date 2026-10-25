<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan identitas toko & logo ke seluruh tampilan (public & admin)
        View::composer('*', function ($view) {
            $view->with('siteName', Setting::get('store_name', 'Bloom Moment'));
            $view->with('siteTagline', Setting::get('tagline', 'Artisan Florist & Botanicals'));
            $view->with('siteLogo', Setting::logoUrl());
            $view->with('sitePhone', Setting::get('phone', '0812-9988-7766'));
            $view->with('siteAddress', Setting::get('address', 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan'));
        });
    }
}

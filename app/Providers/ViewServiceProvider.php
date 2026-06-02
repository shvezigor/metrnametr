<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Catalog;
use App\Models\Type;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $catalog = Catalog::published()->get();
            $types = Type::get();
            $view
                ->with('menuCatalog', $catalog)
                ->with('menuTypes', $types);
        });
    }
}

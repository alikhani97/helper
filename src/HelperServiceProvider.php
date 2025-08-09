<?php

namespace Alikhani\Helper;

use Alikhani\Helper\Middlewares\Mobile;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'responser');
    }

    public function register()
    {
        $this->loadMiddlewares();
    }

    private function loadMiddlewares()
    {
        app('router')->aliasMiddleware('mobile', Mobile::class);
    }

}

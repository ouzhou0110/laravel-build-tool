<?php

namespace ZhouOu\LaravelTool;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use ZhouOu\LaravelTool\Command\TableInitCommand;

class BuilderProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // 注册生成表组件命令
                TableInitCommand::class,
            ]);
        }

    }
}

<?php

namespace ZhouOu\LaravelTool;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use ZhouOu\LaravelTool\Command\TableInitCommand;
use ZhouOu\LaravelTool\Table\Query\Extend\ApiPaginator;

class BuilderProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 注入api分页
        ApiPaginator::inject();
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

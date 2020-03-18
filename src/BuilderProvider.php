<?php

namespace ZhouOu\LaravelTool;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use ZhouOu\LaravelTool\Command\TableInitCommand;
use ZhouOu\LaravelTool\Table\Query\Extend\ApiPaginator;
use ZhouOu\LaravelTool\Tool\FileTool;
use ZhouOu\LaravelTool\Traits\ApiResponseInjector;

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

        // 注入命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                // 注册生成表组件命令
                TableInitCommand::class,
            ]);
        }

        // 注入trait--只能一次注入，多次不同注入同一个文件，会导致原来注入数据丢失
        $this->_injectTrait();

    }

    private function _injectTrait()
    {
        // 向controller注入ApiResponseInjector
        $this->_baseInject(
            ApiResponseInjector::class,
            './app/Http/Controllers/Controller.php',
            __DIR__ . './Inject/ControllerInject.inject');

    }

    private function _baseInject(string $injectTag, string $aimFilePath, string $injectFilePath)
    {

        if (true == file_exists($aimFilePath)) {
            $oldContent = file_get_contents($aimFilePath);
            if (false !== strpos($oldContent, $injectTag)) {
                FileTool::msg("$injectTag 已经注册，只对开发有效");
                return;
            }
        }

        if (false == file_exists($injectFilePath)) {
            FileTool::msg('包文件丢失，请更新');
            return;
        }

        FileTool::save($aimFilePath, file_get_contents($injectFilePath));
    }
}

<?php

namespace ZhouOu\LaravelTool;

use Illuminate\Support\ServiceProvider;
use ZhouOu\LaravelTool\Auth\Auth;
use ZhouOu\LaravelTool\Command\TableInitCommand;
use ZhouOu\LaravelTool\Log\LogInjector;
use ZhouOu\LaravelTool\Table\Query\Extend\ApiPaginator;
use ZhouOu\LaravelTool\Tool\ConfigTool;
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

        // 绑定auth
        $this->app->singleton('auth', function() {
            return $this->app->make(Auth::class);
        });
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

    /**
     * @Function: _injectTrait
     * @Notes: 同一管控注入那些东西
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  20:19
     *
     */
    private function _injectTrait()
    {
        // 向controller注入ApiResponseInjector
        if (ConfigTool::get('zhouOuConfig')['is_inject_api_response'] == false) {
            $this->_baseInject(
                ApiResponseInjector::class,
                '../app/Http/Controllers/Controller.php',
                __DIR__ . './Inject/ControllerInject.inject');
        }

        // 是否使用自己的log
        if (ConfigTool::get('zhouOuConfig')['is_open_log'] == true) {
            $this->_baseInject(
                LogInjector::class,
                '../vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/HandleExceptions.php',
                __DIR__ . './Inject/HandleExceptionsInject.inject'
            );
        } else {
            $this->_baseInject(
                LogInjector::class,
                '../vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/HandleExceptions.php',
                __DIR__ . './Inject/OldHandleExceptionsInject.inject',
                true
            );
        }

    }

    /**
     * @Function: _baseInject
     * @Notes: 基础注入方法
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  20:19
     *
     * @param string $injectTag
     * @param string $aimFilePath
     * @param string $injectFilePath
     *
     * @param bool $isReset
     */
    private function _baseInject(string $injectTag, string $aimFilePath, string $injectFilePath, bool $isReset = false)
    {
        if (true == file_exists($aimFilePath)) {
            $oldContent = file_get_contents($aimFilePath);
            if (false !== strpos($oldContent, $injectTag) && $isReset == false) {
                return;
            } else if (false === strpos($oldContent, $injectTag) && $isReset == true) {
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

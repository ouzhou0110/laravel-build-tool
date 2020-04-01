<?php


namespace ZhouOu\LaravelTool\Auth;


use Illuminate\Support\Facades\Facade;

class AuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'oAuth';
    }
}

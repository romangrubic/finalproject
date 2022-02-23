<?php

class Request
{
    // Getting the user route so that we can use it to point to correct controller and method
    public static function getRoute()
    {
        $route='/';

        if(isset($_SERVER['REDIRECT_PATH_INFO'])){
            $route=$_SERVER['REDIRECT_PATH_INFO'];
        } else if(isset($_SERVER['REQUEST_URI'])){
            $route=$_SERVER['REQUEST_URI'];
        }

        return $route;
    }
}
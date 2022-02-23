<?php

class App
{
public static function start()
    {
        $route=Request::getRoute();

        echo $route;
    }
}
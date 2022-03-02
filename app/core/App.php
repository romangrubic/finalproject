<?php

class App
{
    public static function start()
    {
        // Taking the user url and splitting it into parts
        $route=Request::getRoute();
        $parts=explode(DIRECTORY_SEPARATOR, $route);

        // Next part takes parts[1] and adds Controller so that it can point to a certain controller in app/controller folder
        $class='';
        if(!isset($parts[1]) || $parts[1]===''){
            $class='Index';
        } else {
            $class=ucfirst($parts[1]);
        }
        $class .= 'Controller';

        // This one takes parts[2] and sets the name of the method
        $method='';
        if(!isset($parts[2]) || $parts[2]==''){
            $method='index';
        } else {
            $method=lcfirst($parts[2]);
        }

        // Now we check if the class and method in the same class exist and activate it. Otherwise we show 404error page.
        if(class_exists($class) && method_exists($class, $method)){
            $instance=new $class();
            $instance->$method();
        } else {
            $view = new View();
            $view->render('error404', [
                'missing' => $class . '->' . $method
            ]);
        }
    }

    // Method for easy extraction data from config.php file
    public static function config($key)
    {
        $config=include BP_APP . 'config.php';
        return $config[$key];
    }

    // Method for checking if user is authorized
    public static function authorized()
    {
        if(isset($_SESSION) && isset($_SESSION['authorized'])){
            return true;
        }
        return false;
    }
}
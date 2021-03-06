<?php

/**
 * Created by PhpStorm.
 * User: Я
 * Date: 07.05.2017
 * Time: 0:27
 */
class Router
{
    private $routes;

    public function __construct(){
        $routesPath = ROOT.'/config/routes.php';
        $this->routes = include($routesPath);
    }

//    Returns request string

    private function getURI(){
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    public function run (){

        $uri = $this->getURI();

        foreach ($this->routes as $uriPattern=>$path) {

            if (preg_match("~$uriPattern~", $uri)) {

                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);

//                Define controller and action that handling request
                $segments = explode('/', $internalRoute);

                $controllerName = array_shift($segments).'Controller';

                $controllerName = ucfirst($controllerName);

                $actionName = 'action'.ucfirst(array_shift($segments));

                $parameters = $segments;


//                include file of class-controller
               $controllerFile = ROOT . '/controllers/' .
                   $controllerName . '.php';

               if (file_exists($controllerFile)) {
                   include_once ($controllerFile);
               }

//               create an object and call the action function
               $controllerObject = new $controllerName;
               $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
               if ($result != null) {
                   break;
               }
            }

        }

    }

}
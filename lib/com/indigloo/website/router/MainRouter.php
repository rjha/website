<?php

    namespace com\indigloo\website\router {
        
        use \com\indigloo\Configuration as Config ;
        use \com\indigloo\Logger  as Logger ;

        class MainRouter extends \com\indigloo\core\Router {

            function __construct() {
                // construct routing table
                $this->createRule('^/$', 'com\indigloo\website\controller\Home');
            }

            function route() {
               
                $originalURI = $_SERVER['REQUEST_URI'];
                $requestURI = $originalURI ;

                $pos = strpos($originalURI, '?');
                $qpart = NULL ;

                if($pos !== false) {
                    // remove the part after ? from Url
                    // routing does not depends on query parameters
                    $requestURI = substr($originalURI,0,$pos);
                    $qpart = substr($originalURI, $pos+1);
                }

                $route = $this->getRoute($requestURI);

                if(is_null($route)) {
                    // No valid route for this path
                    $message = sprintf("No route for path %s",$requestURI);
                    Logger::getInstance()->error($message);

                    $controller = new \com\indigloo\website\controller\Http404();
                    $controller->process();
                    exit;

                } else {
                    $controllerName = $route["action"];
                    // add path and query part
                    $options = array();
                    $options["path"] = $requestURI ;
                    $options["query"] = $qpart;
                    $route["options"] = $options ;

                    if(Config::getInstance()->is_debug()) {
                        $message = sprintf("controller %s :: path is %s  ", $controllerName, $requestURI);
                        Logger::getInstance()->debug($message);
                        Logger::getInstance()->dump($route);
                    }

                    $controller = new $controllerName();
                    $controller->process($route["params"], $route["options"]);

                }
            }

        }

    }

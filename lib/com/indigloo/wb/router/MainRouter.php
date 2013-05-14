<?php
     
    namespace com\indigloo\wb\router {
        
        use \com\indigloo\Configuration as Config ;
        use \com\indigloo\Logger  as Logger ;

        class MainRouter extends \com\indigloo\core\Router {

            function __construct() {
                // construct routing table
                $this->createRule('^/$', 'com\indigloo\wb\controller\Home');
                $this->createRule('^(?P<year>[\d]{4})/(?P<month>[\d]{2})/$','com\indigloo\wb\controller\Archive');
                $this->createRule('^tag/(?P<tag>[-\w]+)/$','com\indigloo\wb\controller\Tag');

                $this->createRule('^category/(?P<category>[-\w]+)/$','com\indigloo\wb\controller\Category');
                $this->createRule('^post/(?P<post_id>\d+)/(?P<token>[-\w]+)$','com\indigloo\wb\controller\Post');
                $this->createRule('^(?P<token>[-\w]+)$','com\indigloo\wb\controller\Page');
                // for historical reasons only!
                $this->createRule('^(?P<token>[-\w]+)/$','com\indigloo\wb\controller\Page');
            
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

                /*
                if((strlen($requestURI) > 1) 
                    && (strcmp($requestURI[strlen($requestURI)-1], "/") == 0 )) {
                    // remove trailing slash?
                    // remove trailing slash has implications for
                    // router rules.
                    $requestURI = rtrim($requestURI,"/") ;
                } */

                $route = $this->getRoute($requestURI);

                if(is_null($route)) {
                    // No valid route for this path
                    $message = sprintf("No route for path %s",$requestURI);
                    Logger::getInstance()->error($message);

                    $controller = new \com\indigloo\wb\controller\Http404();
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

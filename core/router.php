<?php

/**
 * Abstract class for control routes
 */
abstract class router {
    const ANY = '*', DEF = '/';
    protected static $_routes = array();
    protected static $_notFoundCallback;

    /**
     * Initialize router
     */
    public static function initialize(){
        if(config::Get("config.ini")->read("router", "auto", 0) == 1)
            self::generate();
        else if(file_exists(APP_ROUTES)){
            $routes = file_get_contents(APP_ROUTES);
            foreach((array)explode("\n", str_replace("\r", "", $routes)) as $route){
                if(strlen($route)>0)
                    if($route[0] == "#")
                        continue;
                $route_raw = explode(" ", $route);
                if(sizeof($route_raw)===3)
                    self::addRoute($route_raw[0], $route_raw[1], $route_raw[2]);
            }
        }
    }

    /**
     * Dynamic add route in memory
     * @param string $method Request method
     * @param string $path Route path
     * @param string $controller Route destination controller and action
     */
    protected static function addRoute($method, $path, $controller){
        $index = self::DEF;//(strlen($path) > 1 && $path[1] != "(" && $path[1] != "[") ? substr($path, 0, 2) : self::DEF;
        if(!isset(self::$_routes[$index]))
            self::$_routes[$index] = array();
        if(strtolower($method) == "any")
            $method = "*";
        self::$_routes[$index][] = array($method, $path, $controller);
    }

    /**
     * Generate route file (and restore it into memory)
     */
    protected static function generate(){
        $routes = "";

        foreach((array)glob(APP_CONTROLLERS."/*.php") as $controller){
            $ctrl = basename($controller, ".php");
            $function_attrs = attribute::parse($controller, $ctrl);
            $routes .= string::format("# Controller {0}\r\n", $ctrl);
            foreach((array)$function_attrs as $func => $attributes){
                if(!isset($attributes["route"])) continue;
                $method = self::ANY;
                $route = $attributes["route"];

                if(isset($attributes["method"]))
                    $method = strtoupper($attributes["method"]);

                $routes .= $method;
                $routes .= string::format(" {0} {1}.{2}\r\n", $attributes["route"], $ctrl, $func);
                self::addRoute($method, $route, $ctrl.".".$func);

            }
            $routes .= "\r\n";
        }
        file_put_contents(APP_ROUTES, trim($routes));
    }

    /**
     * Set url not found handler
     * @param callback $callback function
     * @return bool success
     */
    public static function setNotFoundHandler($callback){
        if(is_callable($callback)){
            self::$_notFoundCallback = $callback;
            return true;
        }
        return false;
    }

    /**
     * Proceed current request to controller
     */
    public static function proceed($uri = null){
        if(!$uri)
            $uri = $_GET['uri'];
        $route = self::find($uri);
        if($route){
            list($method, $uri_mask, $controller_action_raw) = $route;
            $controller_action = explode(".", $controller_action_raw);
            if(sizeof($controller_action) <= 1)
                throw new exceptions\InvalidControllerReferenceException("Invalid controller reference in routes file \"".$controller_action_raw."\"");
            list($controller, $action) = $controller_action;

            $controller_file = APP_CONTROLLERS."/".$controller.".php";
            if(!file_exists($controller_file))
                throw new exceptions\InvalidControllerException("Invalid controller file \"".$controller_file."\"");

            require_once $controller_file;

            if(!class_exists($controller))
                throw new exceptions\InvalidControllerException("Invalid controller \"".$controller."\" controller not implemented");

	        $break = null;
	        $arguments = array();

	        $precalle = $controller."::action";
	        if(is_callable($precalle))
		        $break = call_user_func_array($precalle, array(&$action, &$arguments));

            $callback = $controller."::".$action;
	        $controller_action_raw = $controller.".".$action;

            if(!is_callable($callback))
                throw new exceptions\InvalidControllerActionException("Invalid controller action \"".$controller_action_raw."\" action not implemented");

            $class_ref = new ReflectionClass($controller);
            $method_ref = $class_ref->getMethod($action);
            if(!$method_ref->isStatic())
                throw new exceptions\InvalidControllerActionException("Invalid action method type \"".$controller_action_raw."\" must be Static");

	        if(is_array($arguments) && sizeof($arguments) == 0) {
	            $arguments_match = array();
	            preg_match_all('|\['.$uri_mask.'/\]|Uis', '[/'.$uri.'/]', $arguments_match);
	            for($argId = 1; $argId < sizeof($arguments_match); $argId++)
	                if(isset($arguments_match[$argId][0]))
	                    $arguments[] = $arguments_match[$argId][0];
	        }

            $proceed = true;

            if(config::Get("config.ini")->read("base", "attributes", 0)){
                if(config::Get("config.ini")->read("router", "auto", 0) != 1)
                    attribute::parse(APP_CONTROLLERS."/".$controller.".php", $controller);
                $attributes = attribute::get($controller, $action);
                if($attributes != null){
                    foreach((array)$attributes as $attr => $attr_params){
                        $attr_file = APP_ATTRIBUTES."/".$attr.".php";
                        if(file_exists($attr_file)){
                            require_once $attr_file;
                            $attr_ref = new ReflectionClass($attr);
                            if($attr_ref->isSubclassOf("IAttribute")){
                                $ctrl_data = array(
                                    "controller" => $class_ref,
                                    "action" => $method_ref
                                );
                                $attr_binder = array($attr, "bind");
                                call_user_func_array($attr_binder, array(
                                    $ctrl_data,
                                    $attr_params,
                                    &$arguments,
                                    &$proceed
                                ));
                            }
                        }
                    }
                }
            }

            if($proceed){
			    $result = "";
	            if($break !== false)
                    $result = call_user_func_array($callback, $arguments);
                if($result instanceof IActionResult)
                    $result->render();
                elseif(is_string($result))
                    echo $result;
                /*else
                    throw new \exceptions\InvalidControllerActionException("Controller ".$controller_action_raw." must return IActionResult");*/
            }
            if(defined("DEBUG")) $GLOBALS["_UNIT"]["ENGINE"]["BOOT"] = microtime(1) - $GLOBALS["_performance_mts"];
            return true;
        }
        elseif(is_callable(self::$_notFoundCallback)){
            $result = call_user_func(self::$_notFoundCallback, $uri);
            if($result instanceof IActionResult)
                $result->render();
            return $result;
        }
        else
            throw new exceptions\RouteNotFoundException("Route for url \"/".htmlspecialchars($uri)."\" not found");


    }

    /**
     * Find route by uri
     * @param string $uri
     * @return array|null
     */
    protected static function find($uri){
        return self::findEx($uri, $_SERVER['REQUEST_METHOD']);
    }

    /**
     * Find route by uri and method
     * @param string $uri
     * @param string $method
     * @return array|null route or null (if not found)
     */
    protected static function findEx($uri, $method = self::DEF){
        $_uri = '[/'.$uri.'/]';
        $index = self::DEF;
        /*if(strlen($uri) > 0){
            $index = '/'.substr($uri, 0, 1);
            if(!isset(self::$_routes[$index]))
                $index = self::DEF;
        }*/
        if(!isset(self::$_routes[$index])) return null;
        foreach((array) self::$_routes[$index] As $route)
            if( $route[0] == $method || $route[0] == self::ANY ){
                $arguments = array();
                preg_match_all('|\['.$route[1].'/\]|Uis', $_uri, $arguments);
                if(isset($arguments[0]))
                    if(isset($arguments[0][0]))
                        return $route;
            }
        return null;
    }
}
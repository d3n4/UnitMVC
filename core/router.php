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
            /*
            global $conf;
            if($conf->read("router", "auto", 1) == 1){
                return self::generate();
            }
            else
            */
            if(file_exists(APP_ROUTES)){
                $routes = file_get_contents(APP_ROUTES);
                foreach((array)explode("\n", str_replace("\r", "", $routes)) as $route){
                    $route_raw = explode(" ", $route);
                    if(sizeof($route_raw)===3){
                        $index = (strlen($route_raw[1]) > 1) ? substr($route_raw[1], 0, 2) : self::DEF;
                        if(!isset(self::$_routes[$index]))
                            self::$_routes[$index] = array();
                        self::$_routes[$index][] = $route_raw;
                    }
                }
            }
        }

        protected static function generate(){
            foreach((array)glob(APP_PATH."/controllers/*.php") as $controller){
                print_r(functionComments::parseFile($controller));
            }
            exit;
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

                $controller_file = APP_PATH."/controllers/".$controller.".php";
                if(!file_exists($controller_file))
                    throw new exceptions\InvalidControllerException("Invalid controller file \"".$controller_file."\"");

                require_once $controller_file;

                if(!class_exists($controller))
                    throw new exceptions\InvalidControllerException("Invalid controller \"".$controller."\" controller not implemented");

                $callback = $controller."::".$action;

                if(!is_callable($callback))
                    throw new exceptions\InvalidControllerActionException("Invalid controller action \"".$controller_action_raw."\" action not implemented");

                $class_ref = new ReflectionClass($controller);
                $method_ref = $class_ref->getMethod($action);
                if(!$method_ref->isStatic())
                    throw new exceptions\InvalidControllerActionException("Invalid action method type \"".$controller_action_raw."\" must be Static");

                $arguments_match = array();
                preg_match_all('|\['.$uri_mask.'/\]|Uis', '[/'.$uri.'/]', $arguments_match);
                $arguments = array();
                for($argId = 1; $argId < sizeof($arguments_match); $argId++)
                    if(isset($arguments_match[$argId][0]))
                        $arguments[] = $arguments_match[$argId][0];

                call_user_func_array($callback, $arguments);
                return true;
            }
            elseif(is_callable(self::$_notFoundCallback))
                return call_user_func(self::$_notFoundCallback, $uri);
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
            if(strlen($uri) > 0){
                $index = '/'.substr($uri, 0, 1);
                if(!isset(self::$_routes[$index]))
                    $index = self::DEF;
            }
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
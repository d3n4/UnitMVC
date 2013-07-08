<?php

    class attribute {

        /**
         * Store list of all parsed attributes
         * @var array attributes
         */
        protected static $attributes = array();

        /**
         * @param string $class class name
         * @param string $function function name
         * @return null|array result
         */
        public static function get($class, $function){
            if(isset(self::$attributes[$class]))
                if(isset(self::$attributes[$class][$function]))
                    return self::$attributes[$class][$function];
            return null;
        }

        /**
         * @param string $file controller file
         * @param null|string $ctrl name of controller (class)
         * @return array result
         */
        public static function parse($file, $ctrl = null){
            $memory = array();
            $attributes = functionComments::parseFile($file);
            foreach($attributes as $func => $attrs){
                $matches = array();
                preg_match_all("/\\@([a-zA-Z0-9_]+)\\s(.*)/", $attrs, $matches, PREG_SET_ORDER);
                $memory[trim($func)] = array();
                foreach((array)$matches as $match)
                    if(isset($match[1]))
                        $memory[$func][strtolower(trim($match[1]))] = isset($match[2]) ? trim($match[2]) : null;
                if($ctrl != null){
                    if(!isset(self::$attributes[$ctrl]))
                        self::$attributes[$ctrl] = array();
                    self::$attributes[$ctrl][$func] = $memory[$func];
                }
            }

            return $memory;
        }
    }
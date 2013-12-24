<?php

    /**
     * Extended session class
     */
    abstract class _session {

        /**
         * @param string|mixed $key Key name
         * @param mixed $value Value
         * @param bool $override Override value, if key already exists
         */
        public static function write($key, $value = 1, $override = true) {
            if(isset($_SESSION[$key]) && $override)
	            $_SESSION[$key] = $value;
            elseif(!isset($_SESSION[$key]))
	            $_SESSION[$key] = $value;
        }

        /**
         * @param string|mixed $key Key name
         * @param mixed $default default value, if key not exists in current session
         * @return mixed Reading result
         */
        public static function read($key, $default = null) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        }
    }
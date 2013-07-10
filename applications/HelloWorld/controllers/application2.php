<?php
    abstract class application2 {

        /**
         * @Route /helloworld
         * @Method ANY
         */
        public static function index(){
            echo "Hello world ;)";
        }

        /**
         * @Route /
         * @Validate string $word
         * @Method ANY
         */
        public static function dword(){
            global $_UNIT;
            print_r($_UNIT);
            return new view("main.html");
        }
    }
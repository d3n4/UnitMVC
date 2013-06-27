<?php
    abstract class application2 {

        /**
         * @route /helloworld
         * @method ANY
         */
        public static function index(){
            echo "Hello world ;)";
        }

        /**
         * @route /([a-zA-Z0-9]+)_dword
         * @validate string $word
         * @Match $word ([0-9]+)
         * @method ANY
         */
        public static function dword($word, $somethink){
            return new view("main.html");
        }
    }
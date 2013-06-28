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
         * @Route /([a-zA-Z0-9]+)_dword
         * @Validate string $word
         * @Match $word
         * @Method ANY
         */
        public static function dword($word){
            $m = new MySQL();
            return new view("main.html");
        }
    }
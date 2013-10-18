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
         * @Method GET
         */
        public static function dword(){
            $view = new view("main.twig");
            $view->set("");
            return $view;
        }
    }
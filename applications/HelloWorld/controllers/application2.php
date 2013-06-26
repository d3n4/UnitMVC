<?php
    abstract class application2 {

        /**
         * @route /helloworld
         * @method ANY
         */
        public static function index(){
            print("Hello world");
        }

        /**
         * @route /([a-zA-Z0-9]+)_dword
         * @method ANY
         */
        public static function dword($word){
            print("Hello ".$word);
        }
    }
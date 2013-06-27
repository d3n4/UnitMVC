<?php
    abstract class application {

        /**
         * @Route /
         * @Method GET
         */
        public static function index(){
            print("hello world");
        }

        public static function index2(){
            print("hello world");
        }

        /**
         * @Route /
         * @Method GET
         */
        public static function index3(){
            print("hello world");
        }

        /**
         * 2
         * @Route /id([0-9]+)
         * @param $id
         * @Method POST
         */
        public static function profile($id){
            //echo "Hello ".$name.", ".$id."!";
        }

        /**
         * @Route /login
         */
        public static function login(){
            //echo "Hello ".$name.", ".$id."!";
        }
    }
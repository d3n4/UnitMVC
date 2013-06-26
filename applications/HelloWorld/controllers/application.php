<?php
    abstract class application {

        /**
         * @route /
         * @method GET
         */
        public static function index(){
            print("hello world");
        }

        public static function index2(){
            print("hello world");
        }

        /**
         * @route /
         * @method GET
         */
        public static function index3(){
            print("hello world");
        }

        /**
         * 1
         * @route /id([0-9]+)
         * @param $id
         */
        /**
         * 2
         * @route /id([0-9]+)
         * @param $id
         */
        public static function profile($id){
            //echo "Hello ".$name.", ".$id."!";
        }

        /**
         * @route /login
         */
        public static function login(){
            //echo "Hello ".$name.", ".$id."!";
        }
    }
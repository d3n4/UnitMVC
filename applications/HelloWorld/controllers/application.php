<?php
    abstract class application {

        /**
         * @route /
         */
        public static function index(){

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
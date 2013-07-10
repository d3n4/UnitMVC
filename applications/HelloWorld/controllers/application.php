<?php
    abstract class application {
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
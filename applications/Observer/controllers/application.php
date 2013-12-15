<?php
    abstract class application {

        /**
         * @route /
         */
        public static function main() {
            Observer::observe(function(MainPage $mainpage) {
                echo "Your username: ".$mainpage->username;
            }, "MainPage");
        }

        /**
         * @route /set/([a-zA-Z0-9]+)
         */
        public static function set($name) {
            Observer::observe(function(MainPage $mainpage) use ($name) {
                $mainpage->username = $name;
                echo "Your new username: ".$mainpage->username;
            }, "MainPage");
        }
    }
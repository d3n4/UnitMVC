<?php
    abstract class application {

        /**
         * @route /
         */
        public static function main() {
            MySQL::init();
            if($session = session::pull()) {
                print_r($session);
            } else {
                print_r("not authed");
            }
        }

        /**
         * @route /bind/([a-zA-Z0-9_]+)
         */
        public static function bind($login) {
            MySQL::init();
            $session = session::push($login);
            session::bind($session);
        }
    }
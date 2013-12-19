<?php
    class session extends model {
        protected static $_tableName = "session";

        /**
         * @mysql_field login
         * @mysql_type varchar(32)
         * @mysql_null NO
         * @mysql_default
         */
        public $login;

        /**
         * @mysql_field session
         * @mysql_type varchar(32)
         * @mysql_null NO
         * @mysql_default
         */
        public $session;

        /**
         * @mysql_field time
         * @mysql_type int(11)
         * @mysql_null NO
         * @mysql_default
         */
        public $time;

        /**
         * @mysql_field time
         * @mysql_type int(11)
         * @mysql_null NO
         * @mysql_default -1
         */
        public $lifetime = -1;

        /**
         * @mysql_field agent
         * @mysql_type varchar(128)
         * @mysql_null NO
         * @mysql_default
         */
        public $agent;

        /**
         * @mysql_field ip
         * @mysql_type varchar(15)
         * @mysql_null NO
         * @mysql_default
         */
        public $ip;

        const LOGIN_KEYWORD = "_umlk";
        const SESSION_KEYWORD = "_umsk";
        const SESSION_SALT = "_umss";

        /**
         * Pull current session information
         * @return null|session
         */
        public static function pull() {
            if(crud::isset_all($_SESSION, array(self::LOGIN_KEYWORD, self::SESSION_KEYWORD))) {
                $login = $_SESSION[self::LOGIN_KEYWORD];
                $session = $_SESSION[self::SESSION_KEYWORD];
                $session_model = self::selectOne(_and( eq("login", $login), eq("session", $session) ));
                if(!$session_model) {
                    unset($_SESSION[self::LOGIN_KEYWORD], $_SESSION[self::SESSION_KEYWORD]);
                    return null;
                }
                return $session_model;
            }
            return null;
        }

        /**
         * Push session into database
         * @param string $login unique identifier
         * @return session session object
         */
        public static function push($login) {
            $session = new self();
            $session->login = $login;
            $session->time = time();
            $session->agent = $_SERVER['HTTP_USER_AGENT'];
            $session->ip = $_SERVER['REMOTE_ADDR'];
            $session->session = self::sid($login);
            $session->save();
            return $session;
        }

        /**
         * Bind session to client
         * @param session $session
         */
        public static function bind(session $session) {
            $_SESSION[self::LOGIN_KEYWORD] = $session->login;
            $_SESSION[self::SESSION_KEYWORD] = $session->session;
        }

        /**
         * Generating current session key
         * @param string $login unique identifier
         * @return string session identifier
         */
        public static function sid($login) {
            return strtoupper(
                md5(
                    sha1(
                        $login . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . self::SESSION_SALT
                    )
                )
            );
        }
    }
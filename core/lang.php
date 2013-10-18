<?
    abstract class lang {
        protected static $lang = "en-US";
        protected static $words = array();

        public static function set($locale){
            if(file_exists(APP_LOCALE."/".$locale)){
                self::$words = parse_ini_file(APP_LOCALE."/".$locale);
                self::$lang = $locale;
                return true;
            }
            return false;
        }

        public static function get($word){
            if(isset(self::$words[$word]))
                return self::$words[$word];
            return $word;
        }
    }
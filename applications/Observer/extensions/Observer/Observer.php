<?php
    define("OBSERVE_CACHE", dirname(__FILE__)."/cache");
    define("OBSERVE_MODELS", APP_PATH."/observe");

    index(OBSERVE_MODELS);

    abstract class Observer {
        public static function session() {
            return OBSERVE_CACHE."/".md5(session_id()."observe");
        }

        public static function observe($function, $model) {
            if(is_callable($function)) {
                $model = self::restoreState($model);
                call_user_func($function, $model);
                self::saveState($model);
            }
        }

        protected static function create($model) {
            return new $model();
        }

        protected static function restoreState($model) {
            $sess = self::session();
            if(file_exists($sess)) {
                return unserialize(file_get_contents($sess));
            } else return self::create($model);

        }

        protected static function saveState($model) {
            file_put_contents(self::session(), serialize($model));
        }
    }
<?

    abstract class events {
        protected static $m_events = array();
        
        /**
         * Listen event
         * @param string $event event name
         * @param callback $callback callback
         */
        public static function add($event, $callback){
            self::$m_events[strtoupper(trim($event))] = $callback;
        }
        
        /**
         * Call event
         * @param string $event event name
         * @param array $param_arr callback arguments
         * @return mixed event result
         */
        public static function call($event, $param_arr){
            $callback = self::$m_events[strtoupper(trim($event))];
            if(is_callable($callback))
                return call_user_func_array ($callback, $param_arr);
        }
    }
<?php
    class properties {
        protected $_writable = false;
        public function __get($Name) {
            $callback = array( $this, 'get'.$Name );
            if(is_callable($callback))
                return call_user_func ($callback);
        }

        public function __set($Name, $Value) {
            $callback = array( $this, 'set'.$Name );
            if(is_callable($callback))
                call_user_func ($callback, $Value);
            elseif( $this->_writable === true )
                $this->{$Name} = $Value;
        }

        Public Function __toString() {
            return get_class($this);
        }

        /**
         * Assign properties from object/array
         * @param array|object $object
         * @return self self
         */
        public function assign($object){
            $this->_writable = true;
            foreach((object) $object as $key => $value)
                $this->__set($key, $value);
            $this->_writable = false;
            return $this;
        }
    }
<?php
    class MySQL_result extends properties {
        /**
         * @var mysqli_result
         */
        protected $mysql_result;
        protected $model_class;
        protected $_success = false;

        public function getsuccess() {
            return $this->_success;
        }

        public function __construct($mysql_result, $model_class = null){
            $this->mysql_result = $mysql_result;
            $this->model_class = $model_class;
            if($mysql_result != null)
                $this->_success = true;
            //print_r($this->mysql_result->fetch_object());
        }

        public function next() {
            if(!$this->_success) return null;
            return $this->mysql_result->fetch_object($this->model_class);
        }

        public function free() {
            if($this->mysql_result)
                $this->mysql_result->close();
        }
    }
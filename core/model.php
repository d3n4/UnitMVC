<?php

    abstract class model extends properties {
        /**
         * @var MySQL
         */
        public static $_mysql = null;
        protected static $_tableName;
        protected $_id = null;
        protected $_reserve = array();

        public function __construct($tableName = null) {
            $this->_writable = true;
            if($tableName != null)
                $this->tableName = $tableName;
        }

        public static function tableName() {
            $_tableName = "";
            eval('$_tableName = '.get_called_class().'::$_tableName;');
            return $_tableName;
        }

        public function getid() {
            return $this->_id;
        }

        public function setid($id) {
            $this->_id = $id;
        }

        public function get($key) {
            return $this->{$key};
        }

        public function set($key, $value) {
            $this->{$key} = $value;
            return $this;
        }

        public static function count($query = null, $group = null) {
            $result = self::$_mysql->select(self::tableName(), ($query != null ? $query." " : "").($group != null ? "GROUP BY " . self::$_mysql->escape($group) : ""), ($group != null ? array($group, "COUNT(*)") : array("COUNT(*)")))->fetch_array();
            if($result != null)
                return isset($result[0]) ? $result[0] : 0;
            return 0;
        }

        public function remove() {
            return self::$_mysql->remove(self::tableName(), eq("id", $this->_id));
        }

        public static function select($query = null, $fields = "*", $limit = 0) {
            return new MySQL_result(self::$_mysql->select(self::tableName(), $query, $fields, $limit), get_called_class());
        }

        public static function selectOne($query = null, $fields = "*") {
            return self::select($query, $fields, 1)->next();
        }

        public function save() {
            $signature = get_object_vars($this);
            if($this->_id != null) {
                $diff = crud::array_diff_assoc_recursive($signature, $this->_reserve);
                $diff["_id"] = $this->_id;
                self::$_mysql->update(self::tableName(), $diff);
            } else {
                self::$_mysql->insert(self::tableName(), $this);
                $this->_id = self::$_mysql->lastId();
            }
            $this->_reserve = $signature;
        }
    }
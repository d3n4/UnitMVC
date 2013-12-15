<?php

    class MySQL {

        protected static $instance = null;

        /**
         * @var mysqli
         */
        protected $connection;

        /**
         * @var string
         */
        protected $host;

        /**
         * @var string
         */
        protected $database;

        /**
         * @var string
         */
        protected $user;

        /**
         * @var string
         */
        protected $password;

        /**
         * MySQL Driver constructor
         * @param string $database MySQL database name
         * @param string $host MySQL server host address
         * @param string $user MySQL server user name
         * @param string $password MySQL server user password
         */
        public function __construct($database, $host = "127.0.0.1", $user = "root", $password = "root") {
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
            self::$instance = $this;
        }

        public static function init() {
            if(self::$instance != null)
                return self::$instance;
            $user = config::Get("config.ini")->read("mysql", "user", "root");
            $password = config::Get("config.ini")->read("mysql", "password", "root");
            $database = config::Get("config.ini")->read("mysql", "database", "project");
            $host = config::Get("config.ini")->read("mysql", "host", "127.0.0.1");
            $mysql = new MySQL($database, $host, $user, $password);
            $mysql->connect();
            $mysql->connection->set_charset(config::Get("config.ini")->read("mysql", "charset", "utf8"));
            if(model::$_mysql === null)
                model::$_mysql = $mysql;
            return $mysql;
        }

        public static function get() {
            return self::$instance;
        }

        /**
         * Connect to MySQL server
         * @return bool result
         */
        public function connect() {
            if(!$this->connection)
                $this->connection = new mysqli(
                    $this->host,
                    $this->user,
                    $this->password,
                    $this->database
                );
            return !$this->connection->connect_errno;
        }

        /**
         * Disconnect from MySQL server
         */
        public function disconnect() {
            if($this->connection)
                $this->connection->close();
        }

        public function escape($input) {
            return $this->connection->real_escape_string(
                //addslashes(
                    str_replace(array("`"),array("\\`"), $input)
                //)
            );
        }

        public function getValue($val) {
            switch(strtolower(gettype($val))) {
                case "string":
                    if($val[0] == "#")
                        return '`'.$this->escape(substr($val, 1)).'`';
                    else
                        return "'".$this->escape($val)."'";
                case "boolean":
                    return $val ? "true" : "false";
                case "float":
                case "double":
                case "integer":
                    return $val;
                case 'object':
                case 'array':
                    return "'".json_encode($val)."'";
                case "null":
                default:
                    return 'NULL';
            }
        }

        public function serialize($object) {
            $keys = "";
            $values = "";
            foreach((object)$object As $key => $value ) {
                if(is_numeric($key) || $key[0] == "_") continue;
                if( strlen($keys) > 0 )
                    $keys .= ", ";
                $keys .= "`".$key."`";
                if( strlen($values) > 0 )
                    $values .= ", ";

                $values .= $this->getValue($value);
            }

            return "(".$keys.") VALUES (".$values.")";
        }

        public function serialize_update($object) {
            $result = "";
            foreach((object)$object As $key => $value ) {
                if(is_numeric($key) || $key[0] == "_") continue;
                if( strlen($result) > 0 )
                    $result .= ", ";
                $result .= "`".$key."` = ".$this->getValue($value);
            }

            return $result;
        }

        public function formatWhat($what) {
            $result = "";
            foreach((array)$what as $key) {
                if(strlen($result) > 0)
                    $result .= ", ";
                if(strpos($key, "(") > -1)
                    $result .= $key;
                else
                    $result .= "`".$key."`";
            }
            return $result;
        }

        public function lastId() {
            return $this->connection->insert_id;
        }

        public function buildQuery() {
            $params = func_get_args();
            $args = array(
                array($this, "escape")
            );
            return call_user_func_array("string::process_format", array_merge($args, $params));
        }

        protected function exec($query) {
            return $this->connection->query($query);
        }

        public function query() {
            $query = call_user_func_array(array($this, "buildQuery"), func_get_args());
            // print_r("Query: ".$query."\r\n");
            return $this->exec($query);
        }

        public function insert($table, $object) {
            return $this->query("INSERT INTO `{0}` ".$this->serialize($object).";", $table);
        }

        public function update($table, $object) {
            return $this->query("UPDATE `{0}` SET ".$this->serialize_update($object)." WHERE `id` = {1};", $table, is_array($object) ? $object["_id"] : $object->id);
        }

        protected static function formatWhere($selector = null, $limit = 0) {
            $where = "";
            if($selector !== null)
                $where =  strlen($selector)  > 0 ? " WHERE ". $selector : "";
            if($limit > 0)
                $where = limit($where, $limit);
            return $where;
        }

        public function select($table, $selector = null, $fields = "*", $limit = 0) {
            if($fields === null)
                $fields = "*";
            return $this->query("SELECT ".($fields != "*" ? $this->formatWhat($fields) : "*")." FROM `{0}`".self::formatWhere($selector, $limit).";", $table);
        }

        public function remove($table, $selector = null, $limit = 0) {
            return $this->query("DELETE FROM `{0}`".self::formatWhere($selector, $limit).";", $table);
        }
    }

    function mysql_join($separator, $args) {
        $condition = "";
        $s = sizeof($args);
        for($i=0;$i<$s;$i++) {
            $expression = $args[$i];
            $condition .= $expression . ($i+1<$s?" ".$separator." ":"");
        }
        return $condition;
    }

    function where(){
        return mysql_join("and", func_get_args());
    }

    function _and() {
        return call_user_func_array("where", func_get_args());
    }

    function _or() {
        return mysql_join("or", func_get_args());
    }

    function like($k, $v) {
        return "`".$k."`"." LIKE ".MySQL::get()->getValue($v);
    }

    function eq($k, $v) {
        return "`".$k."`"." = ".MySQL::get()->getValue($v);
    }

    function gt($k, $v) {
        return "`".$k."`"." > ".MySQL::get()->getValue($v);
    }

    function lt($k, $v) {
        return "`".$k."`"." < ".MySQL::get()->getValue($v);
    }

    function gtEq($k, $v) {
        return "`".$k."`"." >= ".MySQL::get()->getValue($v);
    }

    function ltEq($k, $v) {
        return "`".$k."`"." <= ".MySQL::get()->getValue($v);
    }

    function add($k1, $k2) {
        return "`".$k1."`"." + `".$k2."`";
    }

    function sub($k1, $k2) {
        return "`".$k1."`"." - `".$k2."`";
    }

    function limit($input, $num) {
        return $input." LIMIT ".$num;
    }
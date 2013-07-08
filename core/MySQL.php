<?php

    class MySQL {

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
        public function __construct($database, $host = "localhost", $user = "root", $password = ""){
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
        }

        /**
         * Connect to MySQL server
         * @return bool result
         */
        public function connect(){
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
        public function disconnect(){
            if($this->connection)
                $this->connection->close();
        }

        public function query(){
            $query = call_user_func_array("string::format", func_get_args());
            return $this->connection->query($query);
        }
    }
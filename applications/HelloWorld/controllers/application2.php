<?php
    require_once "class-Clockwork.php";
    abstract class application2 {

        /**
         * @Route /helloworld
         * @Method ANY
         */
        public static function index(){
            echo "Hello world ;)";
        }

        /**
         * @Route /
         * @Validate string $word
         * @Method ANY
         */
        public static function dword(){
            /*$m = new MySQL("hardlook");
            $m->connect();
            $q = $m->query("SELECT * FROM users ORDER by ID DESC");
            while ($row = $q->fetch_assoc()) {
                print_r($row);
            }
            $q->free();
           /*if($q){
                echo $q->num_rows;
                $q->close();
            }*/

            /*$mysqli = new mysqli("localhost", "root", "", "hardlook");

            if (mysqli_connect_errno()) {
                printf("Соединение не удалось: %s\n", mysqli_connect_error());
                exit();
            }

            $query = "SELECT * FROM users ORDER by ID DESC";

            if ($result = $mysqli->query($query)) {

                while ($row = $result->fetch_assoc()) {
                    print_r($row);//printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
                }

                $result->free();
            }

            $mysqli->close();*/


            return new view("main.html");
        }
    }
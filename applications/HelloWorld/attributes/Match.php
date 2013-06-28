<?php
    abstract class Match implements IAttribute {
        public static function bind($ctrl, $params, &$arguments, &$proceed){
            echo "Attribute binded to ".$ctrl["controller"]->name;
        }
    }
<?php

    abstract class attributes {
        public static function Parse($file){
            $memory = array();
            $attributes = functionComments::parseFile($file);
            foreach($attributes as $func => $attrs){
                $matches = array();
                preg_match_all("/\\@([a-zA-Z0-9_]+)\\s(.*)/", $attrs, $matches, PREG_SET_ORDER);
                $memory[trim($func)] = array();
                foreach((array)$matches as $match)
                    if(isset($match[1]))
                        $memory[$func][strtolower(trim($match[1]))] = isset($match[2]) ? trim($match[2]) : null;
            }
            return $memory;
        }
    }
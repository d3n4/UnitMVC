<?php

    abstract class functionComments {
        protected $_tokens;
        public static function parseFile($file, $functions = true){
            return self::parse(file_get_contents($file), $functions);
        }
        public static function parse($content, $functions = true){
            $tokens = token_get_all($content);
            $functions = array();
            $nt = false; $comm = ""; $func = ""; $funcs = 0; $comms = 0;
            foreach((array)$tokens as $i=>$token){
                if($token[0] === T_DOC_COMMENT /*&& !$nt*/){
                    $comms++;
                    $comm = $token[1];
                    $nt = true;
                }

                if($token[0] === T_FUNCTION && $nt){
                    $funcs++;
                    $func = $tokens[$i+2][1];
                    $nt = false;
                } elseif($token[0] === T_VARIABLE && $nt){
                    $funcs++;
                    $func = $tokens[$i][1];
                    $func = substr($func,1);
                    $nt = false;
                }

                if($funcs >= 1 and $comms >= 1){
                    $funcs = 0; $comms = 0;
                    $functions[$func] = $comm;
                }
            }
            return $functions;
        }
    }
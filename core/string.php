<?
    /**
     * String extension class
     */
    abstract class string {
        
        /**
         * Empty char
         */
        Const EmptyChar = '';
        
        /**
         * Cut string by length from left
         * @param string $string input
         * @param int $length length
         * @return string cut result
         */
        public static function cutLeft($string, $length){
            return substr($string, $length, strlen($string));
        }
        
        /**
         * Cut string by length from right
         * @param string $string input
         * @param int $length length
         * @return string cut result
         */
        public static function cutRight($string, $length){
            return substr($string, 0, strlen($string) - $length);
        }
        
        /**
         * Cut string by length from end
         * @param string $string input
         * @param int $left left length
         * @param int $right left length
         * @return string cut result
         */
        public static function cut($string, $left, $right){
            return self::cutLeft(self::cutRight($string, $right), $left);
        }
        
        /**
         * Check is string start with delimiter
         * @param string $delimiter Delimiter
         * @param string $string String
         * @return boolean Is "$string" start with "$delimiter"
         */
        public static function startWith($delimiter, $string){
            For($i = 0; $i < strlen($delimiter); $i++)
                IF($string[$i] !== $delimiter[$i]) return false;
            return true;
        }
        
        /**
         * Check is string end with delimiter
         * @param string $delimiter Delimiter
         * @param string $string String
         * @return boolean Is "$string" end with "$delimiter"
         */
        public static function endWith($delimiter, $string){
            For($dIndex = strlen($delimiter)-1; $dIndex > -1; $dIndex--)
                IF($delimiter[$dIndex] !== $string[strlen($string) - (strlen($delimiter)-$dIndex)]) return false;
            return true;
        }
        
        /**
         * Append "$input" to the end of "$string"
         * @param string $input
         * @param string $string
         * @return string append result
         */
        public static function append($input, &$string){
            $string = $string.$input;
            return $string;
        }
        
        /**
         * Prepend "$input" to the string of "$string"
         * @param string $input
         * @param string $string
         * @return string Prepend result
         */
        public static function prepend($input, &$string){
            $string = $input.$string;
            return $string;
        }
        
        /**
         * Format text
         * @param string $input format string
         * @return string format result
         */
        public static function format($input){
            For($i = 1; $i < sizeof(func_get_args()); $i++)
                $input = str_replace ('{'.($i-1).'}', func_get_arg($i), $input);
            return $input;
        }

        public static function check($pattern, $subject){
            $matches = null;
            preg_match($pattern, $subject, $matches);
            return isset($matches[0]) ? $matches[0] == $subject : false;
        }

        public static function is_valid_email_address($email){
            $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
            $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
            $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
                '\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
            $quoted_pair = '\\x5c[\\x00-\\x7f]';
            $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
            $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
            $domain_ref = $atom;
            $sub_domain = "($domain_ref|$domain_literal)";
            $word = "($atom|$quoted_string)";
            $domain = "$sub_domain(\\x2e$sub_domain)*";
            $local_part = "$word(\\x2e$word)*";
            $addr_spec = "$local_part\\x40$domain";
            return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
        }
    }
<?php
    abstract class crud {
        public static function getAssetPath($file) {
            return HOME."/assets/".$file;
        }

        public static function array_diff_assoc_recursive($array1, $array2) {
            foreach($array1 as $key => $value) {
                if($key[0] == "_") continue;
                if(is_array($value)) {
                    if(!isset($array2[$key])) {
                        $difference[$key] = $value;
                    }
                    elseif(!is_array($array2[$key])) {
                        $difference[$key] = $value;
                    }
                    else {
                        $new_diff = crud::array_diff_assoc_recursive($value, $array2[$key]);
                        if($new_diff != false)
                            $difference[$key] = $new_diff;
                    }
                }
                elseif(!isset($array2[$key]) || $array2[$key] != $value) {
                    $difference[$key] = $value;
                }
            }
            return !isset($difference) ? 0 : $difference;
        }

        public static function isset_all($model, $keys) {
            foreach((array)$keys as $key)
                if(!isset($model[$key])) return false;
            return true;
        }
    }
<?php

    /**
     * Class Config
     */
    class config {
        /**
         * @var array
         */
        protected $_storage;

        /**
         * @var string
         */
        protected $_file;

        /**
         * @var Config[]
         */
        protected static $_configs = array();

        /**
         * Get instance of created config object
         * @param string $alias name of config alias
         * @return config|null config
         */
        public static function Get($alias)
        {
            return isset(self::$_configs[$alias]) ? self::$_configs[$alias] : null;
        }

        /**
         * Construct new configuration file
         * @param string $file filename
         * @param string $name alias name
         */
        public function config($file, $name = null){
            $this->_file = $file;
            $this->load();
            if($name == null)
                $name = $file;
            self::$_configs[trim($name)] = $this;
        }

        /**
         * Load configuration file into memory
         * @return array storage raw
         * @throws Exceptions\FileNotFoundException
         */
        public function load(){
            if(file_exists($this->_file))
                return $this->_storage = parse_ini_file ($this->_file, true);
            throw new \Exceptions\FileNotFoundException("File \"".$this->_file."\" not found");
        }

        /**
         * Save current configuration file
         * @param bool $returnOnly
         * @return string
         */
        public function save($returnOnly = false){
            $content = "";
            foreach((array)$this->_storage as $section=>$keys){
                $content .= "[".$section."]\r\n";
                foreach((array)$keys as $key=>$value){
                    if(is_array($value))
                        foreach((array)$value as $val)
                            $content .= $key."[] = ".$val."\r\n";
                    else
                        $content .= $key." = ".$value."\r\n";
                }
                $content .= "\r\n";
            }
            $content = trim($content);
            if(!$returnOnly)
                file_put_contents($this->_file, $content);
            return $content;
        }

        /**
         * Write into configuration storage
         * @param string $section Section
         * @param string $key Key
         * @param string $value Value
         */
        public function write($section, $key, $value){
            if(isset($section, $key, $value))
                $this->_storage[$section][$key] = $value;
        }


        /**
         * Read from storage
         * @param string $section Section
         * @param string $key Key
         * @param mixed $default Default value
         * @return mixed
         */
        public function read($section, $key, $default = null){
            if(isset($this->_storage[$section]))
                if(isset($this->_storage[$section][$key]))
                    return $this->_storage[$section][$key];
            return $default;
        }
    }
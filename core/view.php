<?php

    /**
     * Class view
     */
    class view implements IActionResult {
        protected $tpl;
        protected $loader;
        protected $content;
        protected $memory;

        /**
         * Load view
         * @param string $name view name
         * @param array $memory base variables
         * @throws exceptions\TemplateNotFoundException
         */
        public function view($name, $memory = array()){
            $this->memory = $memory;
            $this->set("HOME", HOME);
            $file = APP_VIEWS."/".$name;
            if(!file_exists($file) || !is_file($file))
                throw new exceptions\TemplateNotFoundException(string::format("Template \"{0}\" not found", $name));
            $this->loader = new Twig_Loader_String();
            $this->tpl = new Twig_Environment($this->loader);
            $this->content = file_get_contents($file);
        }

        /**
         * Set template variables
         * @param string|array|object $name
         * @param null|mixed $value
         */
        public function set($name, $value = null){
            if(is_array($name) || is_object($name))
                foreach($name as $k=>$v)
                    $this->memory[$k] = $v;
            elseif(is_string($name))
                $this->memory[$name] = $value;
        }

        /**
         * Get variable value
         * @param string $name
         * @return null|mixed
         */
        public function get($name){
            if(isset($this->memory[$name]))
                return $this->memory[$name];
            return null;
        }

        /**
         * Compile script and print result
         * @return string|void
         */
        public function render(){
            echo $this->tpl->render($this->content, $this->memory);
        }
    }
<?php

    /**
     * Class view
     */
    class view implements IActionResult {
        protected $twig;
        protected $tpl;
        protected $loader;
        protected $memory;
        protected $options;

        /**
         * Load view
         * @param string $name view name
         * @param array $memory base variables
         * @param boolean $cache enable template caching
         * @throws exceptions\TemplateNotFoundException
         */
        public function view($name, $memory = array(), $cache = false){
            $this->memory = $memory;
            $this->set("HOME", HOME);
            $file = APP_VIEWS."/".$name;
            if(!file_exists($file) || !is_file($file))
                throw new exceptions\TemplateNotFoundException(string::format("Template \"{0}\" not found", $name));
            $this->loader = new Twig_Loader_Filesystem(APP_VIEWS);
            $this->options = array();
            if($cache === true)
                $this->options["cache"] = APP_CACHE . "/views";
            $this->twig = new Twig_Environment($this->loader, $this->options);
            $this->twig->addFilter(new Twig_SimpleFilter('lang', array("lang", "get")));
            $this->tpl = $this->twig->loadTemplate($name);
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
            echo $this->tpl->render($this->memory);
        }

        /**
         * Compile template
         * @return string Compiled template code
         */
        public function compile(){
            return $this->tpl->render($this->memory);
        }
    }
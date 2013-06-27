<?php
    define ("_BASE_", str_replace("\\", "/", dirname(__FILE__)));
    define ("_CORE_", _BASE_."/core");

    error_reporting(E_ALL);
    session_start();

    $indexes = array();
    spl_autoload_register(function($class) use (&$indexes){
        $path = '/'.str_replace("\\","/", $class).".php";

        if(is_file($file = _CORE_.'/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')){
            require_once $file;
            return;
        }

        if(file_exists(_CORE_.$path))
        {
            require_once _CORE_.$path;
            return;
        }
        elseif(file_exists(_BASE_.$path))
        {
            require_once _BASE_.$path;
            return;
        }

        foreach((array)$indexes as $index){
            $fHandle = $index."/".$class.".php";
            if(file_exists($fHandle))
            {
                require_once $fHandle;
                return;
            }
        }

        ExceptionHandler::SimulateException(new \exceptions\ClassNotFoundException("Class ".$class." not found"));
    });

    $conf = new Config("config.ini");

    define ("APP_NAME", $conf->read('app', 'name'));
    define ("APP_PATH", _BASE_."/applications/".APP_NAME);
    define ("APP_ROUTES", APP_PATH."/routes");
    define ("APP_CONTROLLERS", APP_PATH."/controllers");
    define ("APP_MODELS", APP_PATH."/models");
    define ("APP_VIEWS", APP_PATH."/views");
    define ("APP_ASSETS", APP_PATH."/assets");
    define ("HOME", $conf->read("base", "home", "/"));

    if(isset($_GET["asset"])){
        if(!assets::get($_GET["asset"])){
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    } elseif(isset($_GET["uri"])) {
        $indexes[] = APP_CONTROLLERS;
        $indexes[] = APP_MODELS;
        foreach((array)glob(_CORE_."/extensions/*") as $ext)
            if(is_dir($ext))
                $indexes[] = $ext;

        if($conf->read('debug', 'display', 1))
            ExceptionHandler::Initialize();

        ini_set('display_errors', $conf->read('debug', 'display_errors', 1));
        ini_set('display_startup_errors', $conf->read('debug', 'display_errors', 1));
        set_time_limit($conf->read('debug', 'timeout', 0));

        router::initialize();
        router::proceed();
    }
<?php
    define ("_BASE_", str_replace("\\", "/", dirname(__FILE__)));
    define ("_CORE_", _BASE_."/core");

    $indexes = array();

    spl_autoload_register(function($class) use (&$indexes){
        $path = '/'.str_replace("\\","/", $class).".php";
        if(file_exists(_CORE_.$path))
        {
            require_once _CORE_.$path; return;
        }
        elseif(file_exists(_BASE_.$path))
        {
            require_once _BASE_.$path; return;
        }

        foreach((array)$indexes as $index){
            $fHandle = $index."/".$class.".php";
            if(file_exists($fHandle))
            {
                require_once $fHandle; return;
            }
        }

        ExceptionHandler::SimulateException(new \exceptions\ClassNotFoundException("Class ".$class." not found"));
    });

    $conf = new Config("config.ini");

    define ("APP_NAME", $conf->read('app', 'name'));
    define ("APP_PATH", _BASE_."/applications/".APP_NAME);
    define ("APP_ROUTES", APP_PATH."/routes");

    $indexes[] = APP_PATH."/controllers";

    if($conf->read('debug', 'display', 1))
        ExceptionHandler::Initialize();

    ini_set('display_errors', $conf->read('debug', 'display_errors', 1));
    ini_set('display_startup_errors', $conf->read('debug', 'display_errors', 1));
    set_time_limit($conf->read('debug', 'timeout', 0));
    error_reporting(E_ALL);
    session_start();

    router::initialize();

    $dt = microtime(1);
    //for($i=0;$i<50000;$i++)
        router::proceed();
    $dt = microtime(1)-$dt;
    die($dt);
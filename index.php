<?php
    define("DEBUG", 0);

    if(defined("DEBUG")){
        $_performance_mts = microtime(1);
        $_performance_mem = memory_get_usage();
    }

    define ("_BASE_", str_replace("\\", "/", dirname(__FILE__)));
    define ("_CORE_", _BASE_."/core");
	
    error_reporting(E_ALL);
    session_start();

    $_UNIT = array(
        "ENGINE" => array(
            "SCRIPTS" => array(),
            "INDEX" => array(),
            "ELAPSED" => 0,
            "BOOT" => 0
        ),
    );

    spl_autoload_register(function($class) use (&$_UNIT, &$_performance_mem){
        $path = '/'.str_replace("\\","/", $class).".php";

        $script = null;

        if(file_exists(_CORE_.$path))
            $script = _CORE_.$path;
        elseif(file_exists(_BASE_.$path))
            $script = _BASE_.$path;
        elseif(is_file($file = _CORE_.'/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php'))
            $script = $file;
        else foreach((array)$_UNIT["ENGINE"]["INDEX"] as $index){
            $fHandle = $index."/".$class.".php";
            if(file_exists($fHandle))
            {
                $script = $fHandle;
                break;
            }
        }

        if($script !== null){
            if(defined("DEBUG")){
                $start = microtime(1);
                require_once $script;
                $elapsed = microtime(1)-$start;
                $_performance_mem = memory_get_usage()-$_performance_mem;
                $_UNIT["ENGINE"]["SCRIPTS"][] = array(
                    "file" => $script,
                    "size" => filesize($script),
                    "memory" => $_performance_mem,
                    "time" => $elapsed
                );
                $_UNIT["ENGINE"]["ELAPSED"] += $elapsed;
            } else require_once $script;
        } else ExceptionHandler::SimulateException(new \exceptions\ClassNotFoundException("Class ".$class." not found"));
    });
    
    $_UNIT["CONFIG"] = new Config("config.ini");

    function unit_conf(){ return $GLOBALS["_UNIT"]["CONFIG"]; }

    define ("APP_NAME", $_UNIT["CONFIG"]->read('app', 'name'));
    define ("APP_PATH", _BASE_."/applications/".APP_NAME);
    define ("APP_ROUTES", APP_PATH."/routes");
    define ("APP_CONTROLLERS", APP_PATH."/controllers");
    define ("APP_MODELS", APP_PATH."/models");
    define ("APP_VIEWS", APP_PATH."/views");
    define ("APP_ASSETS", APP_PATH."/assets");
    define ("APP_ATTRIBUTES", APP_PATH."/attributes");
    define ("APP_EXTENSIONS", APP_PATH."/extensions");

    $tmp = explode("/", $_SERVER['REQUEST_URI']);
    unset($tmp[sizeof($tmp)-1]);
    define ("URI", implode("/", $tmp));
    $tmp = explode("/", $_SERVER["SCRIPT_NAME"]);
    unset($tmp[sizeof($tmp)-1]);
    define ("HOME", implode("/", $tmp));

    if(isset($_GET["asset"])){
        assets::setVar("HOME", HOME);
        assets::setVar("URI", URI);
        if(!assets::get($_GET["asset"])){
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    } elseif(isset($_GET["uri"])) {
        $_UNIT["ENGINE"]["INDEX"][] = APP_CONTROLLERS;
        $_UNIT["ENGINE"]["INDEX"][] = APP_MODELS;
        if($_UNIT["CONFIG"]->read("base", "attributes", 0))
            $_UNIT["ENGINE"]["INDEX"][] = APP_ATTRIBUTES;
        foreach((array)glob(APP_EXTENSIONS."/*") as $ext)
            if(is_dir($ext))
                $_UNIT["ENGINE"]["INDEX"][] = $ext;

        if($_UNIT["CONFIG"]->read('debug', 'display', 1))
            ExceptionHandler::Initialize();

        ini_set('display_errors', $_UNIT["CONFIG"]->read('debug', 'display_errors', 1));
        ini_set('display_startup_errors', $_UNIT["CONFIG"]->read('debug', 'display_errors', 1));
        set_time_limit($_UNIT["CONFIG"]->read('debug', 'timeout', 0));
        router::initialize();
        router::proceed();
        print_r( $GLOBALS["_UNIT"] );
    }
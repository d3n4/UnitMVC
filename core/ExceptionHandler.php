<?
Abstract Class ExceptionHandler {
    Protected Static Function ShowError($ErrorTitle, $ErrorDescription, $ErrorFile, $ErrorLine, $ErrorLines, $ErrorBackTrace, $SkipLine = 1)
    {
        IF(isset($_REQUEST['ignore']))
            return;
        IF(ob_get_length() > 0)
            ob_end_clean();
        $ErrorTitle = htmlspecialchars($ErrorTitle);
        $ErrorTitle = explode("\\", $ErrorTitle);
        $ErrorTitle = $ErrorTitle[sizeof($ErrorTitle)-1];
        $ErrorFile = htmlspecialchars($ErrorFile);
        $ErrorLine = htmlspecialchars($ErrorLine);
        /*ForEach($ErrorLines as $errLn => $ErrorCode)
            $ErrorLines[$errLn] = str_replace(array("\r", "\n", '<span', '</span>', '<?', '?>'), array('<p', '</p>', '', ''), highlight_string('<?'.$ErrorCode.'?>', true));*/
        //print_r($ErrorLines);
        ob_start();
        ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?=$ErrorTitle?></title>
        <link rel="shortcut icon" href="http://babin.at.ua/warning_16-1-.png" />
        <style>
            a
            {
                color: #730000;
            }
            html, body, pre
            {
                margin: 0;
                padding: 0;
                font-family: Monaco, 'Lucida Console';
                background: #ECECEC;
            }

            h1
            {
                margin: 0;
                background: #A31012;
                padding: 20px 45px;
                color: #fff;
                text-shadow: 1px 1px 1px rgba(0,0,0,.3);
                border-bottom: 1px solid #690000;
                font-size: 28px;
            }

            p#detail
            {
                margin: 0;
                padding: 15px 45px;
                background: #F5A0A0;
                border-top: 4px solid #D36D6D;
                color: #730000;
                text-shadow: 1px 1px 1px rgba(255,255,255,.3);
                font-size: 14px;
                border-bottom: 1px solid #BA7A7A;
            }

            p#detail input
            {
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#AE1113), to(#A31012));
                border: 1px solid #790000;
                padding: 3px 10px;
                text-shadow: 1px 1px 0 rgba(0, 0, 0, .5);
                color: white;
                border-radius: 3px;
                cursor: pointer;
                font-family: Monaco, 'Lucida Console';
                font-size: 12px;
                margin: 0 10px;
                display: inline-block;
                position: relative;
                top: -1px;
            }

            h2
            {
                margin: 0;
                padding: 5px 45px;
                font-size: 12px;
                background: #333;
                color: #fff;
                text-shadow: 1px 1px 1px rgba(0,0,0,.3);
                border-top: 4px solid #2a2a2a;
            }

            pre
            {
                margin: 0;
                border-bottom: 1px solid #DDD;
                text-shadow: 1px 1px 1px rgba(255,255,255,.5);
                position: relative;
                font-size: 12px;
                overflow: hidden;
            }

            pre span.line
            {
                text-align: right;
                display: inline-block;
                padding: 5px 5px;
                width: 30px;
                background: #D6D6D6;
                color: #8B8B8B;
                text-shadow: 1px 1px 1px rgba(255,255,255,.5);
                font-weight: bold;
            }

            pre span.code
            {
                padding: 5px 5px;
                position: absolute;
                right: 0;
                left: 40px;
            }

            pre:first-child span.code
            {
                border-top: 4px solid #CDCDCD;
            }
            pre:first-child span.line
            {
                border-top: 4px solid #B6B6B6;
            }
            pre.error span.line
            {
                background: #A31012;
                color: #fff;
                text-shadow: 1px 1px 1px rgba(0,0,0,.3);
            }

            pre.error
            {
                color: #A31012;
                
            }
            
            pre.error span.marker
            {
                background: #A31012;
                color: #fff;
                text-shadow: 1px 1px 1px rgba(0,0,0,.3);
                font-weight: bold;
            }
            
            .errorline
            {
                background: #FFFFFF;
            }
            .ehBtn {
                -moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
                box-shadow:inset 0px 1px 0px 0px #ffffff;
                background-color:#ff0000;
                border:1px solid #800000;
                display:inline-block;
                color:#ffffff;
                font-family:arial;
                font-size:12px;
                font-weight:bold;
                padding:5px 10px;
                text-decoration:none;
                text-shadow:1px 1px 0px #750000;
            }.ehBtn:hover {
                color: #FFF;
                background-color:#ff3636;
            }.ehBtn:active {
                position:relative;
                top:1px;
            }
            .IgnoreApb {
                color: #FFF;
                font-size: 12px;
                position: relative;
                top: -10px;
                left: -15px;
                text-decoration: none;
            }
            .IgnoreApb:hover {
                color: #FFF;
                text-decoration: none;
            }
        </style>
    </head>

    <body>
        <h1><?=join(" ", string::splitUpperWords($ErrorTitle))?> <a class="IgnoreApb" href="?ignore=1">Ignore</a></h1>
        <p id="detail">
            <?=$ErrorDescription?>
        </p>

        <h2>In <?=$ErrorFile?> at line <?=$ErrorLine?>.</h2>
        <div>
            <? $ln = 0; ForEach($ErrorLines as $Line=>$Code){ $ln++;
            $Code = str_replace( array("\r", "\n", '&lt;?begin', 'end?&gt;', '<code>', '</code>'), '', highlight_string('<?begin'.$Code.'end?>', true));
            $color = 'D00';
            $Code = str_replace( array('&lt;?','?&gt;'), array('<span style="color: #'.$color.'">&lt;?</span>', '<span style="color: #'.$color.'">?&gt;</span>'), $Code );
            $iserrln = false;
            IF( $Line == $ErrorLine )
            {
                $iserrln = true;
                /*$Code = explode('">', $Code);
                $Tabs = explode('<', $Code[2]);
                $Tabs = $Tabs[0];
                unset($Code[2]);
                $Marker = $Code[3][0];
                $Code[3][0] = '';
                $Code = implode('">', $Code);
                $Code = $Tabs.'<span class="marker">'.$Marker.'</span>'.$Code;*/
            }
            ?>
            <pre<? IF($iserrln){ ?> class="error" <? } ?>><span class="line"><?=$Line?></span><span class="code<?IF($iserrln){?> errorline<?}?>"><?=$Code?></span></pre>
            <? }

            Function RenameFunction($function){
                $_externalCall = '<span style="color:#d00005;"><b>[ External call ]</b></span>';
                return str_replace( array("call_user_func_array", "call_user_func"), array($_externalCall,$_externalCall), $function );
            }

            ForEach ($ErrorBackTrace as $i=>$entry)
                IF($entry['function'] == 'SimulateError' or $entry['function'] == 'SimulateException') unset($ErrorBackTrace[$i]);
            IF($ErrorBackTrace && sizeof($ErrorBackTrace)>0){ ?>
            <h2>Backtrace</h2>
            <div>
                <?
                $ecline = 1;
                $trace = "";
                ForEach ($ErrorBackTrace as $entry) 
                {
                    IF(isset($entry['class']))
                        $trace = $entry['class'].'::'.$entry['function'];
                    ELSE IF(isset($entry['function']))
                        $trace =  $entry['function'];

                    if($entry['function'] == "call_user_func" or $entry['function'] == "call_user_func_array"){
                        /*
                        $args = $entry['args'];
                        if(isset($args[0][0]))
                            if(is_object($args[0][0]))
                                $trace = get_class($args[0][0]);
                            else
                                $trace = $args[0][0];
                        if(isset($args[0][1]))
                            $trace .= '::'.$args[0][1];
                        */
                        continue;
                    } else {
                        $trace .= " (";

                        $fargs = '';

                        IF(sizeof($entry['args'])>0)
                        {
                            $fargs .= ' ';
                            ForEach((array)$entry['args'] as $argId=>$arg)
                            {
                                $fargs .= self::getArgument ($arg);
                                IF( $argId < sizeof($entry['args'])-1 )
                                    $fargs .= ', ';
                            }
                            $fargs .= ' ';
                        }

                        $trace .= $fargs.')';
                    }

                    $trace .= '<small style="color: gray;"> ';
                    IF(isset($entry['file']))
                        $trace .= $entry['file'];
                    ELSE
                        $trace .= $ErrorFile;
                    $trace .= '</small>';
                    $Error = $ecline == $SkipLine;
                    IF($Error)
                    {
                        $marker = $trace[0];
                        $trace = substr($trace,1,strlen($trace)-1);
                        $trace = '<span class="marker">'.$marker.'</span>'.$trace;
                    }
                    ?>
                    <pre<?IF($Error){?> class="error" <?}?>><span class="line"><?=$ecline?></span><span class="code<?IF($Error){?> errorline<?}?>"><?=$trace?></span></pre>
                    <?
                    $ecline++;
                }
                }
                ?>
            </div>
            <?
            /*<?$Stopwatches = Stopwatch::GetAll(); IF(sizeof($Stopwatches)>0){?>
            <h2>Stopwatches</h2>
            <div>
                <?$time = 0; $Line = 0; ForEach( (Array)$Stopwatches  as $Name=>$Stopwatch ){$diff = $time; $time = $Stopwatch->Stop(); IF($diff > 0) $diff -= $time; ELSE $diff = ''; $iserrln = $Line == 0;?>
                <pre<? IF($iserrln){ ?> class="error" <? } ?>><span class="line"><?=$Line?></span><span class="code<?IF($iserrln){?> errorline<?}?>"><?=$Name?> <b><?=$time?></b> <small style="color: gray;"><?=$diff?></small></span></pre>
                <?$Line++;}?>
            </div>
            <?}?>*/
            ?>
        </div>
    </body>
    </html>
        <?
        $content = ob_get_contents();
        ob_end_clean();
        die($content);
    }

    Protected Static Function getArgument($arg)
    {
        IF($arg === null)
            return 'NULL';
        Switch (strtolower(gettype($arg)))
        {
            case 'string':
                return( '"'.str_replace( array("\n"), array(''), $arg ).'"' );
            case 'boolean':
                return $arg ? 'true' : 'false';
            case 'object':
                return 'object('.get_class($arg).')';
            case 'array':
                $ret = 'array( ';
                $separtor = '';
                foreach ($arg as $k => $v) {
                    $ret .= $separtor.self::getArgument($k).' => '.self::getArgument($v);
                    $separtor = ', ';
                }
                $ret .= ' )';
                return $ret;
            case 'resource':
                return 'resource('.get_resource_type($arg).')';
            default:
                return var_export($arg, true);
        }
    }

    Public Static Function SimulateError($errno, $errstr, $errfile, $errline, $e = null, $SkipLine = 1)
    {
        //IF(!Config::Read('developer', 'throwExceptions', false)) return true;
        # IF(!(error_reporting() & $errno)) return true; #

         $errorType = array (
                   E_ERROR          => 'ERROR',
                   E_WARNING        => 'WARNING',
                   E_PARSE          => 'PARSING ERROR',
                   E_NOTICE         => 'NOTICE',
                   E_CORE_ERROR     => 'CORE ERROR',
                   E_CORE_WARNING   => 'CORE WARNING',
                   E_COMPILE_ERROR  => 'COMPILE ERROR',
                   E_COMPILE_WARNING => 'COMPILE WARNING',
                   E_USER_ERROR     => 'USER ERROR',
                   E_USER_WARNING   => 'USER WARNING',
                   E_USER_NOTICE    => 'USER NOTICE',
                   E_STRICT         => 'STRICT NOTICE',
                   E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
                   );
         
        $err = 'CAUGHT ';
        
        IF($e !== null && gettype($e) == 'object')
            $err .= get_class($e);
        ELSE
            $err .= 'EXCEPTION';
        
        IF (@array_key_exists($errno, $errorType))
            $err = $errorType[$errno];
        
        $file = Array();
        
        IF(file_exists($errfile))
            $file = file($errfile);
        
        $errlines = array();

        $eclines = 10;
        
        For( $ln = $errline - $eclines; $ln <  $errline + $eclines - 1; $ln ++ )
            IF(isset($file[$ln]))
                $errlines[$ln+1] = $file[$ln];
        
        $backtrace = debug_backtrace();
            
        IF(is_object($e))
            $backtrace = $e->getTrace();

        $Skip = Array('filemtime');

        ForEach($backtrace As $trace)
            ForEach($Skip As $Func)
                    IF($trace['function'] == $Func)
                    return true;

        self::ShowError($err, $errstr, $errfile, $errline, $errlines, $backtrace, $SkipLine); 
        return true;
    }

    Public Static Function SimulateException($Exception)
    {
            ForEach($Exception->getTrace() As $SkipLine => $Trace)
                IF(isset($Trace['file']) && isset($Trace['line']))
                    IF(!in_array($Trace['function'], array("call_user_func", "call_user_func_array")))
                        return self::SimulateError($Exception->getCode(), $Exception->getMessage(), $Trace['file'], $Trace['line'], $Exception, $SkipLine + 1);
        
        self::SimulateError($Exception->getCode(), $Exception->getMessage(), $Exception->getFile(), $Exception->getLine(), $Exception);
    }
    
    Public Static Function Initialize(){
        set_error_handler( 'ExceptionHandler::SimulateError', E_ERROR | E_WARNING | E_PARSE | 
                                    E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | 
                                    E_COMPILE_ERROR | E_COMPILE_WARNING | 
                                    E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | 
                                    E_STRICT | E_RECOVERABLE_ERROR );

        set_exception_handler( 'ExceptionHandler::SimulateException' );
    }
}

Function SecureExit(){
    ExceptionHandler::SimulateError ('0', 'Security error', '.Kernel', 26);
}
<?php

    abstract class debug {
        public static function render(){
            ?>
            <html>
            <head>
                <title>UnitMVC debug</title>
                <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
                <script src="https://google-code-prettify.googlecode.com/svn/loader/prettify.js"></script>

                <style>
                    html, body { padding: 0; margin: 0; }
                    .container {
                        min-width: 640px;
                        width: 640px;
                        height: 320px;
                        overflow: hidden;
                    }

                    .errorline { background-color: #4D3232; border-radius: 3px; }
                    #sourcecode { position: relative; margin: 0; padding: 0; }
                    pre.prettyprint { padding-top: 10px; padding-bottom: 10px; }
                    .var { color: #678CB1; } .str { color: #EC7600; } .kwd { color: #93C763; } .com { color: #66747B; } .typ { color: #678CB1; } .lit { color: #FACD22; } .pun { color: #F1F2F3; } .pln { color: #F1F2F3; } .tag { color: #8AC763; } .atn { color: #E0E2E4; } .atv { color: #EC7600; } .dec { color: purple; } pre.prettyprint { border: 0px solid #888; } ol.linenums { margin-top: 0; margin-bottom: 0; } .prettyprint { background: #293134; } li.L0, li.L1, li.L2, li.L3, li.L4, li.L5, li.L6, li.L7, li.L8, li.L9 { color: #81969A; list-style-type: decimal; } @media print { .str { color: #060; } .kwd { color: #006; font-weight: bold; } .com { color: #600; font-style: italic; } .typ { color: #404; font-weight: bold; } .lit { color: #044; } .pun { color: #440; } .pln { color: #000; } .tag { color: #006; font-weight: bold; } .atn { color: #404; } .atv { color: #060; } }
                </style>
            </head>                                                                                                                                                                                        ..

            <body>
            an unexpected exception has occurred
                <div class="container">
                    <pre id="sourcecode" class="prettyprint linenums">class attribute {

    /**
     * Store list of all parsed attributes
     * @var array attributes
     */
    protected static $attributes = array();

    /**
     * @param string $class class name
     * @param string $function function name
     * @return null|array result
     */
    public static function get($class, $function){
        if(isset(self::$attributes[$class]))
            if(isset(self::$attributes[$class][$function]))
                return self::$attributes[$class][$function];
        return null;
    }

    /**
     * @param string $file controller file
     * @param null|string $ctrl name of controller (class)
     * @return array result
     */
    public static function parse($file, $ctrl = null){
        $memory = array();
        $attributes = functionComments::parseFile($file);
        foreach($attributes as $func => $attrs){
            $matches = array();
            preg_match_all("/\\@([a-zA-Z0-9_]+)\\s(.*)/", $attrs, $matches, PREG_SET_ORDER);
            $memory[trim($func)] = array();
            foreach((array)$matches as $match)
                if(isset($match[1]))
                    $memory[$func][strtolower(trim($match[1]))] = isset($match[2]) ? trim($match[2]) : null;
            if($ctrl != null){
                if(!isset(self::$attributes[$ctrl]))
                    self::$attributes[$ctrl] = array();
                self::$attributes[$ctrl][$func] = $memory[$func];
            }
        }

        return $memory;
    }
}</pre>
                </div>
                <script>
                    $(function(){
                        prettyPrint();
                        $(".pln").each(function(i, e){
                            var $e = $(e);
                            var kw = $e.html();
                            var word = kw.trim().toLowerCase();
                            if(word[0] == "$")
                                $e.removeClass("pln").addClass("var");
                            else if(word == "array" || word == "isset")
                                $e.removeClass("pln").addClass("kwd");
                        });
                        var errln = 14;
                        var $ln = $($("#sourcecode > ol > li")[errln-1]);
                        $ln.addClass("errorline");
                        // $("#sourcecode").css("bottom", $(".errorline").position().top - $(".errorline").position().top / 2 - 30 );
                    });
                </script>
            </body>
            </html>

        <?
        }
    }
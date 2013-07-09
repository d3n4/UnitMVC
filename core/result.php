<?php

    class result implements IActionResult {

        public static $codes = array(   100 => "100 Continue",
                                        101 => "101 Switching Protocols",
                                        102 => "102 Processing",
                                        200 => "200 OK",
                                        201 => "201 Created",
                                        202 => "202 Accepted",
                                        203 => "203 Non-Authoritative Information",
                                        204 => "204 No Content",
                                        205 => "205 Reset Content",
                                        206 => "206 Partial Content",
                                        207 => "207 Multi-Status",
                                        226 => "226 IM Used",
                                        300 => "300 Multiple Choices",
                                        301 => "301 Moved Permanently",
                                        302 => "302 Moved Temporarily",
                                        303 => "303 See Other",
                                        304 => "304 Not Modified",
                                        305 => "305 Use Proxy",
                                        307 => "307 Temporary Redirect",
                                        400 => "400 Bad Request",
                                        401 => "401 Unauthorized",
                                        402 => "402 Payment Required",
                                        403 => "403 Forbidden",
                                        404 => "404 Not Found",
                                        405 => "405 Method Not Allowed",
                                        406 => "406 Not Acceptable",
                                        407 => "407 Proxy Authentication Required",
                                        408 => "408 Request Timeout",
                                        409 => "409 Conflict",
                                        410 => "410 Gone",
                                        411 => "411 Length Required",
                                        412 => "412 Precondition Failed",
                                        413 => "413 Request Entity Too Large",
                                        414 => "414 Request-URI Too Large",
                                        415 => "415 Unsupported Media Type",
                                        416 => "416 Requested Range Not Satisfiable",
                                        417 => "417 Expectation Failed",
                                        422 => "422 Unprocessable Entity",
                                        423 => "423 Locked",
                                        424 => "424 Failed Dependency",
                                        425 => "425 Unordered Collection",
                                        426 => "426 Upgrade Required",
                                        428 => "428 Precondition Required",
                                        429 => "429 Too Many Requests",
                                        431 => "431 Request Header Fields Too Large",
                                        449 => "449 Retry With",
                                        451 => "451 Unavailable For Legal Reasons",
                                        456 => "456 Unrecoverable Error",
                                        500 => "500 Internal Server Error",
                                        501 => "501 Not Implemented",
                                        502 => "502 Bad Gateway",
                                        503 => "503 Service Unavailable",
                                        504 => "504 Gateway Timeout",
                                        505 => "505 HTTP Version Not Supported",
                                        506 => "506 Variant Also Negotiates",
                                        507 => "507 Insufficient Storage",
                                        508 => "508 Loop Detected",
                                        509 => "509 Bandwidth Limit Exceeded",
                                        510 => "510 Not Extended",
                                        511 => "511 Network Authentication Required"
        );

        /**
         * @var string
         */
        protected $_content;

        /**
         * @var int
         */
        protected $_code;

        /**
         * @var string
         */
        protected $_message;

        /**
         * @var array
         */
        protected $_headers;

        /**
         * Construct empty ActionResult response
         * @param string $content
         * @param int $code
         * @param array $headers
         */
        public function Result($content, $code, $headers = array()){
            $this->_content = $content;
            $this->_headers = $headers;
            if(isset(self::$codes[$code]))
                $this->_code = $code;
        }

        /**
         * Constructs a 200 OK response containing a text/plain response body.
         * @param string $data response
         * @return Result
         */
        public static function ok($data = ""){
            return new self($data, 200, array("Content-Type", "text/plain"));
        }

        /**
         * Constructs a 404 Not Found response containing a text/plain response body.
         * @param string $data response
         * @return Result
         */
        public static function notFound($data = ""){
            return new self($data, 404, array("Content-Type", "text/plain"));
        }

        /**
         * Constructs a 400 Bad Request response containing a text/plain response body.
         * @param string $data response
         * @return Result
         */
        public static function badRequest($data = ""){
            return new self($data, 400, array("Content-Type", "text/plain"));
        }

        /**
         * Constructs a 500 Internal Server Error response containing a text/plain response body.
         * @param string $data response
         * @return Result
         */
        public static function internalServerError($data = ""){
            return new self($data, 500, array("Content-Type", "text/plain"));
        }

        /**
         * Set response header
         * @param string $key header key
         * @param string $value header value
         */
        public function set($key, $value){
            $this->_headers[$key] = $value;
        }

        /**
         * Constructs a user response containing a text/plain response body.
         * @param integer $code response code
         * @param string $content response
         * @return Result
         */
        public static function status($code, $content){
            return new self($content, $code);
        }

        /**
         * Present response as other mime-type (def. text/plain)
         * @param string $type
         * @return Result $this
         */
        public function which($type = "text/plain"){
            $this->set("Content-Type", $type);
            return $this;
        }

        /**
         * Render result response
         * @return string
         */
        public function render(){
            $this->_message = self::$codes[$this->_code];
            header("HTTP/1.0 ".$this->_message);
            foreach($this->_headers as $key => $value)
                header($key.": ".$value);
            return $this->_content;
        }

        /**
         * Return rendered result
         * @return string
         */
        public function __toString(){
            return $this->_content;
        }
    }
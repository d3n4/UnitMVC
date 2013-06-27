<?php

    interface IAttribute {
        public static function bind($ctrl, $params, &$arguments, &$proceed);
    }
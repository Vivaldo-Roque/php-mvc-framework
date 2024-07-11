<?php

namespace App\Utils;

// Classe responsável por fazer debugs
class Debug
{

    public static function print($variable, $return = false)
    {

        $res = "<pre>" . print_r($variable, return: true) . "</pre>";

        if ($return) {
            return $res;
        }

        echo $res;
        exit;
    }
}

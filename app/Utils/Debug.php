<?php

namespace App\Utils;

// Classe responsável por fazer debugs
class Debug
{

    public static function print($variable, $exit = true)
    {

        $res = "<pre>" . print_r($variable) . "</pre>";

        if (!$exit) {
            echo $res;
            return;
        }

        echo $res;
        exit;
    }
}

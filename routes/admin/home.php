<?php

use \App\Http\Response;
use \App\Controller\Admin;
use \App\Utils\Debug;

// rota admin
$obRouter->get('/admin', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Home::getHome($request));
    }
]);
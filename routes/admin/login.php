<?php

use \App\Http\Response;
use \App\Controller\Admin;
use \App\Utils\Debug;


// rota login
$obRouter->get('/admin/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function($request){
        return new Response(200, Admin\Login::getLogin($request));
    }
]);

// rota login (post)
$obRouter->post('/admin/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function($request){

        // Debug::print($request->getPostVars());

        return new Response(200, Admin\Login::setLogin($request));
    }
]);

// rota logout
$obRouter->get('/admin/logout', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Login::setLogout($request));
    }
]);
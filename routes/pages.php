<?php

use \App\Http\Response;
use \App\Controller\Pages;

// rota home

$obRouter->get('/', [
    'middlewares' => [
        'cache'
    ],
    function($request){
        return new Response(200, Pages\Home::getHome($request));
    }
]);

$obRouter->get('/depoimentos', [
    'middlewares' => [
        'cache'
    ],
    function($request){
        return new Response(200, Pages\Testimony::getTestimonies($request));
    }
]);

$obRouter->post('/depoimentos', [
    function($request){
        return new Response(200, Pages\Testimony::insertTestimony($request));
    }
]);

$obRouter->get('/sobre', [
    'middlewares' => [
        'cache'
    ],
    function($request){
        return new Response(200, Pages\About::getAbout($request));
    }
]);

// rota dinamica

/* 

$obRouter->get('/pagina/{idPagina}/{acao}', [
    function($request, $idPagina, $acao){
        return new Response(200, $idPagina . ' - ' . $acao);
    }
]); 

*/
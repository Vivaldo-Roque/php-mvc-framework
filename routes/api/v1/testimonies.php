<?php

use \App\Http\Response;
use App\Controller\Api;

const contentType = 'application/json';

// Rota de listagem de depoimentos
$obRouter->get('/api/v1/testimonies', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request){
        return new Response(200, Api\Testimony::getTestimonies($request), contentType);
    }
]);

// Rota de consulta individual de depoimentos
$obRouter->get('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request, $id){
        return new Response(200, Api\Testimony::getTestimony($request, $id), contentType);
    }
]);

// Rota de cadastro de depoimentos
$obRouter->post('/api/v1/testimonies', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request){
        return new Response(201, Api\Testimony::setNewTestimony($request), contentType);
    }
]);

// Rota de atualualizacao de depoimentos
$obRouter->put('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(201, Api\Testimony::setEditTestimony($request, $id), contentType);
    }
]);

// Rota de exclusao de depoimentos
$obRouter->delete('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(201, Api\Testimony::setDeleteTestimony($request, $id), contentType);
    }
]);
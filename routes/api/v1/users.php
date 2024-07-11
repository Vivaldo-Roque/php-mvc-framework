<?php

use \App\Http\Response;
use App\Controller\Api;

// Rota de listagem de usuarios
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api',
        'user-basic-auth',
        'cache'
    ],
    function($request){
        return new Response(200, Api\User::getUsers($request), 'application/json');
    }
]);

// Rota de consulta do usuario atual
$obRouter->get('/api/v1/users/me', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request){
        return new Response(200, Api\User::getCurrentUser($request), 'application/json');
    }
]);

// Rota de consulta individual de usuarios
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth',
        'cache'
    ],
    function($request, $id){
        return new Response(200, Api\User::getUser($request, $id), 'application/json');
    }
]);

// Rota de cadastro de usuarios
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request){
        return new Response(201, Api\User::setNewUser($request), 'application/json');
    }
]);

// Rota de atualualizacao de usuarios
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(201, Api\User::setEditUser($request, $id), 'application/json');
    }
]);

// Rota de exclusao de usuarios
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(201, Api\User::setDeleteUser($request, $id), 'application/json');
    }
]);
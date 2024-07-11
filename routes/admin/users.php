<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota de listagem de usuarios
$obRouter->get('/admin/users', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::getusers($request));
    }
]);

// Rota de cadastro de um novo usuario
$obRouter->get('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::getNewUser($request));
    }
]);

// Rota de cadastro de um novo usuario (POST)
$obRouter->post('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::setNewUser($request));
    }
]);

// Rota de edicao de um usuario
$obRouter->get('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::getEditUser($request, $id));
    }
]);

// Rota de edicao de um usuario (POST)
$obRouter->post('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::setEditUser($request, $id));
    }
]);

// Rota de exclusao de um usuario
$obRouter->get('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::getDeleteUser($request, $id));
    }
]);

// Rota de exclusao de um usuario (POST)
$obRouter->post('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::setDeleteUser($request, $id));
    }
]);

<?php

require __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \App\Utils\Environment as Env;
use \App\Utils\Db_Mngr\Database;
use \App\Http\Middleware\Queue as MidddlewareQueue;

//Carrega variaveis de ambiente
Env::load(__DIR__.'/../');

define('URL', getenv('URL'));

// Define as configuracoes de banco de dados
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// Define o valor padrao das variaveis
View::init([
    'URL' => URL,
]);

// Define o mapeamento de middlewares
MidddlewareQueue::setMap(
    [
        'maintenance' => \App\Http\Middleware\Maintenance::class,
        'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
        'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
        'api' => \App\Http\Middleware\Api::class,
        'user-basic-auth' => \App\Http\Middleware\UserBasicAuth::class,
        'jwt-auth' => \App\Http\Middleware\JWTAuth::class,
        'cache' => \App\Http\Middleware\Cache::class
    ]
);

// Define o mapeamento de middlewares padroes em todas as rotas
MidddlewareQueue::setDefaultMiddlewares([
    'maintenance'
],);
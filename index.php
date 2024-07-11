<?php

require __DIR__.'/config/app.php';

use \App\Http\Router;

// Inicia o router
$obRouter = new Router(URL);

// Inclui as rotas de paginas
include __DIR__.'/routes/pages.php';

// Inclui as rotas de paginas admin
include __DIR__.'/routes/admin.php';

// Inclui as rotas da API
include __DIR__.'/routes/api.php';

// Imprime o response da rota
$obRouter->run()->sendResponse();
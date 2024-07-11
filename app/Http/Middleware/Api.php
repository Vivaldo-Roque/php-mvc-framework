<?php

namespace App\Http\Middleware;

use App\Utils\Environment as Env;

class Api {

    /**
     * 
     * Metodo responsavel por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        // Altera o contentType para json
        $request->getRouter()->setContentType('application/json');

        // Executa o proximo nivel do middleware
        return $next($request);
    }

}
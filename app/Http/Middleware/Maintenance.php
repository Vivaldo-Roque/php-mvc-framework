<?php

namespace App\Http\Middleware;

use App\Utils\Environment as Env;

class Maintenance {

    /**
     * 
     * Metodo responsavel por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        // Verifica o estado de manutencao da pagina
        if(Env::bool(getenv('MAINTENANCE'))){
            throw new \Exception("Pagina em manutencao tente novamente mais tarde.", 200);
        }

        // Executa o proximo nivel do middleware
        return $next($request);
    }

}
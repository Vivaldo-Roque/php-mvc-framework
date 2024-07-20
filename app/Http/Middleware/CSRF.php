<?php

namespace App\Http\Middleware;
use App\Session\Security\CSRF as SessionCSRF;
use App\Utils\Debug;
use App\Utils\TwigSingleton;

class CSRF
{

    /**
     * 
     * Metodo responsavel por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {

        // Cria o token csrf a cada request
        SessionCSRF::createToken();
        // Passar o token CSRF para todos os templates
        TwigSingleton::getInstance()->getTwig()->addGlobal('csrf_token', SessionCSRF::getToken());

        // Executa o proximo nivel do middleware
        return $next($request);
    }
}

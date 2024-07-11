<?php

namespace App\Http\Middleware;

use Exception;
use App\Model\Entity\User;

class UserBasicAuth
{

    /**
     * 
     * Metodo responsavel por retornar uma instancia de usuario autenticado
     * @return User
     * 
     */
    private function getBasicAuthUser()
    {
        //Verifica a existencia dos dados de acesso
        if(!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }

        // Busca o usuario pelo email
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);
        
        // Verifica a instancia
        if(!$obUser instanceof User){
            return false;
        }

        // Valida a senha
        $validation = password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha);

        // Retorna o usuario
        return $validation ? $obUser : false;
    }

    /**
     * 
     * Metodo responsavel por validar o acesso via HTTP BASIC AUTH
     * @param Request $request
     * 
     */
    private function basicAuth($request)
    {
        // Verifica o usuario recebido
        if($obUser = $this->getBasicAuthUser()){
            $request->user = $obUser;
            return true;
        }

        // Emite o erro de senha invalida
        throw new Exception("Usuario ou senha invalidos", 403);
    }


    /**
     * 
     * Metodo responsavel por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     * 
     */
    public function handle($request, $next)
    {


        // Realiza a validacao do acesso via basic auth
        $this->basicAuth($request);


        // Executa o proximo nivel do middleware
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Exception;
use App\Model\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{

    /**
     * 
     * Metodo responsavel por retornar uma instancia de usuario autenticado
     * @param Request $request
     * @return User
     * 
     */
    private function getJWTAuthUser($request)
    {

        // Headers
        $headers = $request->getHeaders();

        // token puro em JWT
        $jwt = '';

        if (isset($headers['Authorization'])) {
            $jwt = str_replace('Bearer ', '', $headers['Authorization']);
        }

        try {
            // Decode
            $decoded = (array)JWT::decode($jwt, new Key(getenv('JWT_KEY'), 'HS256'));
        } catch (Exception $e) {
            throw new Exception("Token invalido!", 403);
        }

        // Email
        $email = $decoded['email'] ?? '';

        // Busca o usuario pelo email
        $obUser = User::getUserByEmail($email);

        // Valida o usuario
        $validation = $obUser instanceof User;

        // Retorna o usuario
        return $validation ? $obUser : false;
    }

    /**
     * 
     * Metodo responsavel por validar o acesso via JWT
     * @param Request $request
     * 
     */
    private function auth($request)
    {
        // Verifica o usuario recebido
        if ($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        // Emite o erro de acesso
        throw new Exception("Acesso negado", 403);
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


        // Realiza a validacao do acesso via JWT
        $this->auth($request);


        // Executa o proximo nivel do middleware
        return $next($request);
    }
}

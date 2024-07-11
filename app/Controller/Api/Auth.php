<?php

namespace App\Controller\Api;

use App\Model\Entity\User;
use Exception;
use Firebase\JWT\JWT;

class Auth extends Api {
    /**
     * 
     * Metodo responsavel por gerar um token JWT
     * @param Request $request
     * @return array
     * 
     */
    public static function generateToken($request){
        
        // POST vars
        $postVars = $request->getPostVars();

        // Valida os campos obrigatorios
        $camposObrigatorios = [
            'email',
            'senha'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($postVars[$campo])) {
                throw new Exception("O campo '{$campo}' e obrigatorio", 400);
            }
        }

        // Busca o usuario pelo email
        $obUser = User::getUserByEmail($postVars['email']);

        // Validar se usuario existe
        if(!$obUser instanceof User){
            throw new Exception("O email ou senha sao invalidos", 400);
        }

        // Valida a senha do usuario
        if(!password_verify($postVars['senha'], $obUser->senha)){
            throw new Exception("O email ou senha sao invalidos", 400);
        }

        // PAYLOAD
        $payload = [
            'email' => $obUser->email
        ];

        // Gerar e retornar token JWT
        return[
            'token' => JWT::encode($payload, getenv('JWT_KEY'), 'HS256')
        ];
    }
}
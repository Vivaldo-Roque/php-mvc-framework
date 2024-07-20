<?php

namespace App\Session\Security;

class CSRF
{

    /**
     * 
     * Metodo responsavel por iniciar a sessao
     * 
     */

     private static function init()
     {
         // Verifica se a sessao nao esta ativa
         if (session_status() != PHP_SESSION_ACTIVE) {
             
             // Inicia a sessao
             session_start();
         }
     }

    /**
     * 
     * Metodo responsavel por verificar se existe um token CSRF
     * @return boolean
     * 
     */

    public static function isSetToken()
    {
        // Inicia a sessao
        self::init();

        return isset($_SESSION['csrf']['token']);
    }

    /**
     * 
     * Metodo responsavel por verificar se existe um token CSRF
     * @return boolean
     * 
     */

    public static function createToken()
    {
        // Inicia a sessao
        self::init();

        // Caso ja esta definido
        if (self::isSetToken()) {
            return false;
        }
        
        // Cria o token
        $_SESSION['csrf'] = [
            'token' => bin2hex(random_bytes(32))
        ];

        // sucesso
        return true;
    }

    /**
     * 
     * Metodo responsavel por validar o token do formulário (frontend) com o token armazenado na sessão (backend)
     * @return boolean
     * 
     */

     public static function validateToken($form_token)
     {
         // Inicia a sessao
         self::init();
 
         // Verifique se o token CSRF é inválido
         if (!self::isSetToken() || !hash_equals($_SESSION['csrf']['token'], $form_token)) {
             return false;
         }

         // Deleta a sessao
         unset($_SESSION['csrf']);
 
         // Sucesso
         return true;
     }

    /**
     * 
     * Metodo responsavel por retornar o token
     * @return string
     * 
     */
    public static function getToken(){

        // Inicia a sessao
        self::init();

        if(self::isSetToken()){
            return $_SESSION['csrf']['token'];
        }
        return '';
    }
}

<?php

namespace App\Session\Admin;

class Login {

    /**
     * 
     * Metodo responsavel por iniciar a sessao
     * 
     */

    private static function init(){
        // Verifica se a sessao nao esta ativa
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * 
     * Metodo responsavel por criar o login do usuario
     * @param Usuario $obUsuario
     * @return boolean
     * 
     */

     public static function login($obUsuario){

        // Inicia a sessao
        self::init();

        $_SESSION['admin']['usuario'] = [
            'id' => $obUsuario->id,
            'email' => $obUsuario->email
        ];

        // sucesso

        return true;

     }

     /**
     * 
     * Metodo responsavel por verificar se o usuario esta logado
     * @return boolean
     * 
     */

     public static function isLogged(){
        // Inicia a sessao
        self::init();

        // retorna a verificacao
        return isset($_SESSION['admin']['usuario']['id']);
     }

    /**
     * 
     * Metodo responsavel por executar o logout do usuario
     * @return boolean
     * 
     */

    public static function logout(){
        // Inicia a sessao
        self::init();

        // retorna a verificacao
        unset($_SESSION['admin']['usuario']);

        // sucesso
        return true;
     }
}
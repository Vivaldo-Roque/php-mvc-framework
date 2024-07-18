<?php

namespace App\Controller\Admin;

use App\Model\Entity\User;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Controller\Admin\Alert;

class Login extends Page
{

    /**
     * 
     * Metodo responsavel por retornar a renderizacao da pagina de login
     * @param Request $request
     * @param string $errormessage
     * @return string
     * 
     */

    public static function getLogin($request, $errorMessage = null)
    {
        // status
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        // retorna a pagina completa
        return parent::getPage(title: 'Login > MVC', view: 'admin/login.html', vars: [
            'status' => $status
         ]);
    }

    /**
     * 
     * Metodo responsavel por definir o login do usuario
     * @param Request $request
     * 
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['password'] ?? '';

        // Busca o usuario pelo email
        $obUsuario = User::getUserByEmail($email);

        if (!$obUsuario instanceof User) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        // Verifica a senha do usuario
        if (!password_verify($senha, $obUsuario->senha)) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        // Cria a sessao de login
        SessionAdminLogin::login($obUsuario);

        // Redireciona o usuario para a home do admin
        $request->getRouter()->redirect('/admin');
    }

    /**
     * 
     * Metodo responsavel por deslogar o usuario
     * @param Request $request
     * 
     */

     public static function setLogout($request)
    {
        // Destroi a sessao de login
        SessionAdminLogin::logout();

        // Redireciona o usuario para a pagina de login
        $request->getRouter()->redirect('/admin/login');
    }


}

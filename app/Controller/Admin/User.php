<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\User as EntityUser;
use App\Utils\Db_Mngr\Pagination;
use App\Utils\Debug;

class User extends Page
{


    /**
     * 
     * Metodo responsavel por obter a renderizacao dos items para a pagina
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string 
     * 
     */

    private static function getUserItems($request, &$obPagination)
    {

        // notas
        $itens = '';

        // quantidade total de registos
        $quantidadeTotal = EntityUser::countAll();

        // Pagina atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Total de itens por pagina
        $itensPerPages = 5;

        // instancia de paginacao
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, $itensPerPages);

        // resultado da consulta
        $results = EntityUser::getUsers(limit: $obPagination->getLimit());

        foreach ($results as $obUser) {

            $itens .= View::render('admin/modules/users/widgets/itens', [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ]);
        }

        return $itens;
    }


    /**
     * 
     * Metodo responsavel por renderizar a view de listagem de usuarios (GET)
     * @param Request $request
     * @return string
     * 
     */

    public static function getUsers($request)
    {
        // Conteudo da home
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Usuarios', $content, 'users');
    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de cadastro de um novo usuario 
     * @param Request $request
     * @return string
     * 
     */

    public static function getNewUser($request)
    {
        // Conteudo do formulario
        $content = View::render('admin/modules/users/form', [
            'title' => 'Cadastrar Usuario',
            'status' => self::getStatus($request),
            'required' => 'required'
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Cadastrar usuario', $content, 'users');
    }

    /**
     * 
     * Metodo responsavel por cadastrar um usuario no banco
     * @return string
     * 
     */

    public static function setNewUser($request)
    {

        // Post vars
        $postVars = $request->getPostVars();

        // Nova instancia de Usuario
        $obUser = new EntityUser(
            nome: $postVars['nome'],
            email: $postVars['email'],
            senha: $postVars['senha']
        );

        // Valida o email do usuario
        $obUserEmail = EntityUser::getUserByEmail($obUser->email);
        if ($obUserEmail instanceof EntityUser) {
            // Redireciona o usuario
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        $obUser->cadastrar();

        // Redireciona o usuario
        $request->getRouter()->redirect("/admin/users/{$obUser->id}/edit?status=created");
    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de edicao de um usuario (GET)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function getEditUser($request, $id)
    {

        // Obtem o Usuario do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Conteudo do formulario
        $content = View::render('admin/modules/users/form', [
            'title' => 'Editar usuario',
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Editar usuario', $content, 'users');
    }


    /**
     * 
     * metodo responsavel por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        // Query params
        $queryParams = $request->getQueryParams();

        // Status
        if (!isset($queryParams['status'])) {
            return;
        }

        // Mensagens de status
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Usuario criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuario atualizado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuario deletado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O email digitado ja esta sendo utilizado por outro usuario!');
                break;
        }
    }

    /**
     * 
     * Metodo responsavel por gravar a atualizacao de um Usuario (POST)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function setEditUser($request, $id)
    {

        // Obtem o Usuario do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Post vars
        $postVars = $request->getPostVars();

        // Atualiza a instancia
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = $postVars['senha'];

        // Valida o email do usuario
        $obUserEmail = EntityUser::getUserByEmail($obUser->nome);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            // Redireciona o usuario
            $request->getRouter()->redirect('/admin/users/{$id}/edit?status=duplicated');
        }

        $obUser->atualizar();

        // Redireciona o usuario
        $request->getRouter()->redirect("/admin/users/{$obUser->id}/edit?status=updated");
    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de exclusao de um Usuario (GET)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function getDeleteUser($request, $id)
    {

        // Obtem o Usuario do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Conteudo do formulario
        $content = View::render('admin/modules/users/delete', [
            'nome' => $obUser->nome,
            'email' => $obUser->email,
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Excluir Usuario', $content, 'users');
    }

    /**
     * 
     * Metodo responsavel por excluir um Usuario (POST)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function setDeleteUser($request, $id)
    {

        // Obtem o Usuario do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Excluir o Usuario
        $obUser->excluir();

        // Redireciona o usuario
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}

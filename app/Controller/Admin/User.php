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
        $itens = [];

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

            $itens [] = $obUser->toArray();
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
        // Retorna a pagina completa
        return parent::getPanel(title: 'Usuarios', view: 'admin/modules/users/index.html', vars: [
            'users' => self::getUserItems($request, $obPagination),
            'paginations' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ], currentModule: 'users');
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
        // Retorna a pagina completa
        return parent::getPanel(title: 'Cadastrar usuario', view: 'admin/modules/users/form.html', vars: [
            'status' => self::getStatus($request),
            'required' => 'required'
        ], currentModule: 'users');
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

        // Retorna a pagina completa
        return parent::getPanel(title: 'Editar usuario', view: 'admin/modules/users/form.html', vars: [
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ], currentModule: 'users');
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

        // Retorna a pagina completa
        return parent::getPanel(title: 'Excluir Usuario', view: 'admin/modules/users/delete.html', vars: [
            'nome' => $obUser->nome,
            'email' => $obUser->email,
        ], currentModule: 'users');
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

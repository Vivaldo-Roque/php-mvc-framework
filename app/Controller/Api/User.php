<?php

namespace App\Controller\Api;

use App\Model\Entity\User as EntityUser;
use App\Utils\Db_Mngr\Pagination;
use Exception;

class User extends Api
{

    /**
     * 
     * Metodo responsavel por obter a renderizacao dos items de nota para a pagina
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string 
     * 
     */

    private static function getUserItems($request, &$obPagination)
    {

        // usuarios
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

            $itens[] = [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ];
        }

        return $itens;
    }


    /**
     * 
     * Metodo responsavel por retornar os usuarios cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers($request)
    {
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * 
     * Metodo responsavel por retornar o usuario atualmente conectado
     * @param Request $request
     * @return array
     * 
     */

     public static function getCurrentUser($request)
     {

        // Usuario atual
        $obUser = $request->user;
 
         // Retorna os detalhes do usuario
         return [
             'id' => (int)$obUser->id,
             'nome' => $obUser->nome,
             'email' => $obUser->email
         ];
     }

    /**
     * 
     * Metodo responsavel por retornar os detalhes de um usuario
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function getUser($request, $id)
    {

        // Valida o id do usuario
        if (!is_numeric($id)) {
            throw new Exception("O id {$id} nao e valido", 400);
        }

        // Busca usuario
        $obUser = EntityUser::getUserById($id);

        // Valida se o usuario existe
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuario {$id} nao foi encontrado", 404);
        }

        // Retorna os detalhes do usuario
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * 
     * Metodo responsavel por cadastrar um usuario
     * @param Request $request
     * @return array
     * 
     */

    public static function setNewUser($request)
    {
        // Variaveis do post
        $postVars = $request->getPostVars();

        // Valida os campos obrigatorios
        $camposObrigatorios = [
            'nome',
            'email',
            'senha'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($postVars[$campo])) {
                throw new Exception("O campo '{$campo}' e obrigatorio", 400);
            }
        }

        // Valida a duplicacao de usuarios
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if($obUserEmail instanceof EntityUser){
            throw new Exception("O email '{$postVars['email']}' ja esta em uso!", 400);
        }

        // Novo usuario
        $obUser = new EntityUser(
            nome: $postVars['nome'],
            email: $postVars['email'],
            senha:  $postVars['senha']
        );

        $obUser->cadastrar();

        // Retorna os detalhes do usuario
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * 
     * Metodo responsavel por atualizar um usuario
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function setEditUser($request, $id)
    {
        // Variaveis do post
        $postVars = $request->getPostVars();

        // Valida os campos obrigatorios
        $camposObrigatorios = [
            'nome',
            'email',
            'senha'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($postVars[$campo])) {
                throw new Exception("O campo '{$campo}' e obrigatorio", 400);
            }
        }

        // Valida se o usuario ja existe
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuario {$id} nao foi encontrado", 404);
        }

        // Valida a duplicacao de usuarios
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id){
            throw new Exception("O email '{$postVars['email']}' ja esta em uso!", 400);
        }

        // Atualiza o usuario
        $obUser->setAttrs(
            nome: $postVars['nome'],
            email: $postVars['email'],
            senha: $postVars['senha']
        );

        $obUser->atualizar();

        // Retorna os detalhes do usuario atualizado
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * 
     * Metodo responsavel por excluir um usuario
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function setDeleteUser($request, $id)
    {
        // Valida se o usuario ja existe
        $obUser = EntityUser::getUserById($id);

        // Valida a instancia
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuario {$id} nao foi encontrado", 404);
        }

        // Evitar usuario autenticado via API se excluir
        if($obUser->id == $request->user->id){
            throw new Exception("Nao e possivel excluir o cadastro atualmente conectado", 400);
        }

        // Exclui o usuario
        $obUser->excluir();

        // Retorna os detalhes do usuario excluido
        return [
            'sucesso' => true,
        ];
    }
}

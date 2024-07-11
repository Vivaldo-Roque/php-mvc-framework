<?php

namespace App\Controller\Api;

use App\Model\Entity\Testimony as EntityTestimony;
use App\Utils\Db_Mngr\Pagination;
use App\Utils\Debug;
use Exception;

class Testimony extends Api
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

    private static function getTestemonyItems($request, &$obPagination)
    {

        // depoimentos
        $itens = [];

        // quantidade total de registos
        $quantidadeTotal = EntityTestimony::countAll();

        // Pagina atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Total de itens por pagina
        $itensPerPages = 5;

        // instancia de paginacao
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, $itensPerPages);

        // resultado da consulta
        $results = EntityTestimony::getTestimonies(limit: $obPagination->getLimit());

        foreach ($results as $obTestimony) {

            $itens[] = [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data
            ];
        }

        return $itens;
    }


    /**
     * 
     * Metodo responsavel por retornar os depoimentos cadastrados
     * @param Request $request
     * @return array
     */
    public static function getTestimonies($request)
    {
        return [
            'depoimentos' => self::getTestemonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * 
     * Metodo responsavel por retornar os detalhes de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function getTestimony($request, $id)
    {

        // Valida o id do depoimento
        if (!is_numeric($id)) {
            throw new Exception("O id {$id} nao e valido", 400);
        }

        // Busca depoimento
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida se o depoimento existe
        if (!$obTestimony instanceof EntityTestimony) {
            throw new Exception("O depoimento {$id} nao foi encontrado", 404);
        }

        // Retorna os detalhes do depoimento
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * 
     * Metodo responsavel por cadastrar um depoimento
     * @param Request $request
     * @return array
     * 
     */

    public static function setNewTestimony($request)
    {
        // Variaveis do post
        $postVars = $request->getPostVars();

        // Valida os campos obrigatorios
        $camposObrigatorios = [
            'nome',
            'mensagem'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($postVars[$campo])) {
                throw new Exception("O campo '{$campo}' e obrigatorio", 400);
            }
        }

        // Novo depoimento
        $obTestimony = new EntityTestimony(
            nome: $postVars['nome'],
            mensagem: $postVars['mensagem']
        );

        $obTestimony->cadastrar();

        // Retorna os detalhes do depoimento
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * 
     * Metodo responsavel por atualizar um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function setEditTestimony($request, $id)
    {
        // Variaveis do post
        $postVars = $request->getPostVars();

        // Valida os campos obrigatorios
        $camposObrigatorios = [
            'nome',
            'mensagem'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($postVars[$campo])) {
                throw new Exception("O campo '{$campo}' e obrigatorio", 400);
            }
        }

        // Valida se o depoimento ja existe
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instancia
        if (!$obTestimony instanceof EntityTestimony) {
            throw new Exception("O depoimento {$id} nao foi encontrado", 404);
        }

        // Atualiza o depoimento
        $obTestimony->setAttrs(
            nome: $postVars['nome'],
            mensagem: $postVars['mensagem']
        );

        $obTestimony->atualizar();

        // Retorna os detalhes do depoimento atualizado
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * 
     * Metodo responsavel por excluir um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     * 
     */

    public static function setDeleteTestimony($request, $id)
    {
        // Valida se o depoimento ja existe
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instancia
        if (!$obTestimony instanceof EntityTestimony) {
            throw new Exception("O depoimento {$id} nao foi encontrado", 404);
        }

        // Exclui o depoimento
        $obTestimony->excluir();

        // Retorna os detalhes do depoimento excluido
        return [
            'sucesso' => true,
        ];
    }
}

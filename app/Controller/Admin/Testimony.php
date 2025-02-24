<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Testimony as EntityTestimony;
use App\Utils\Db_Mngr\Pagination;
use App\Utils\Debug;

class Testimony extends Page
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

        // notas
        $itens = '';

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

            $itens .= View::render('admin/modules/testimonies/widgets/itens', [
                'id' => $obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
            ]);
        }

        return $itens;
    }


    /**
     * 
     * Metodo responsavel por renderizar a view de listagem de depoimentos (GET)
     * @param Request $request
     * @return string
     * 
     */

    public static function getTestimonies($request)
    {
        // Conteudo da home
        $content = View::render('admin/modules/testimonies/index', [
            'itens' => self::getTestemonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Depoimentos', $content, 'testimonies');
    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de cadastro de um novo depoimento 
     * @param Request $request
     * @return string
     * 
     */

    public static function getNewTestimony($request)
    {
        // Conteudo do formulario
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Cadastrar depoimento',
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Cadastrar depoimento', $content, 'testimonies');
    }

    /**
     * 
     * Metodo responsavel por cadastrar um depoimento no banco
     * @return string
     * 
     */

    public static function setNewTestimony($request)
    {

        // Post vars
        $postVars = $request->getPostVars();

        // Nova instancia de depoimento
        $obTestimony = new EntityTestimony(
            nome: $postVars['nome'] ?? '',
            mensagem: $postVars['mensagem'] ?? ''
        );

        $obTestimony->cadastrar();

        // Redireciona o usuario
        $request->getRouter()->redirect("/admin/testimonies/{$obTestimony->id}/edit?status=created");

    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de edicao de um depoimento (GET)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function getEditTestimony($request, $id)
    {

        // Obtem o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instancia
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Conteudo do formulario
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Editar depoimento',
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ]);

        // Retorna a pagina completa
        return parent::getPanel('Editar depoimento', $content, 'testimonies');
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
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento deletado com sucesso!');
                break;
        }
    }

    /**
     * 
     * Metodo responsavel por gravar a atualizacao de um depoimento (POST)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function setEditTestimony($request, $id)
    {

        // Obtem o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instancia
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Post vars
        $postVars = $request->getPostVars();

        // Atualiza a instancia
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];

        $obTestimony->atualizar();

        // Redireciona o usuario
        $request->getRouter()->redirect("/admin/testimonies/{$obTestimony->id}/edit?status=updated");
    }

    /**
     * 
     * Metodo responsavel por retornar o formulario de exclusao de um depoimento (GET)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

     public static function getDeleteTestimony($request, $id)
     {
 
         // Obtem o depoimento do banco de dados
         $obTestimony = EntityTestimony::getTestimonyById($id);
 
         // Valida a instancia
         if (!$obTestimony instanceof EntityTestimony) {
             $request->getRouter()->redirect('/admin/testimonies');
         }
 
         // Conteudo do formulario
         $content = View::render('admin/modules/testimonies/delete', [
             'nome' => $obTestimony->nome,
             'mensagem' => $obTestimony->mensagem,
         ]);
 
         // Retorna a pagina completa
         return parent::getPanel('Excluir depoimento', $content, 'testimonies');
     }

     /**
     * 
     * Metodo responsavel por excluir um depoimento (POST)
     * @param Request $request
     * @param integer $id
     * @return string
     * 
     */

    public static function setDeleteTestimony($request, $id)
    {

        // Obtem o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instancia
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Excluir o depoimento
        $obTestimony->excluir();

        // Redireciona o usuario
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }
}
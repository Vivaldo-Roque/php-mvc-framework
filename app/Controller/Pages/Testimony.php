<?php

namespace App\Controller\Pages;

use App\Model\Entity\Testimony as EntityTestimony;
use \App\Utils\View;
use \App\Utils\Db_Mngr\Pagination;

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

   private static function getTestemonyItems($request, &$obPagination){
      
      // notas
      $itens = '';

      // quantidade total de registos
      $quantidadeTotal = EntityTestimony::countAll();

      // Pagina atual
      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      // Total de itens por pagina
      $itensPerPages = 2;

      // instancia de paginacao
      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, $itensPerPages);

      // resultado da consulta
      $results = EntityTestimony::getTestimonies(limit: $obPagination->getLimit());

      foreach($results as $obTestimony){

         $itens .= View::render('pages/testimony/table_item', [
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
         ]);
      }

      return $itens;
   }

   // Extende Page para usar getpage e gerar uma redundancia.

   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    * @return string
    * 
    */

   public static function getTestimonies($request)
   {

      // View de depoimento
      $content = View::render('pages/testimonies', [
         'itens' => self::getTestemonyItems($request, $obPagination),
         'pagination' => parent::getPagination($request, $obPagination)
      ]);

      // Retorna a view da pagina
      return parent::getPage('Depoimentos', $content);
   }

   public static function insertTestimony($request)
   {
      // dados do post
      $postVars = $request->getPostVars();

      if (isset($postVars['enviar'])) {
         $obTestimony = new EntityTestimony(
            nome: $postVars['nome'],
            mensagem: $postVars['mensagem']
         );

         $obTestimony->cadastrar();
      }

      return self::getTestimonies($request);
   }

}

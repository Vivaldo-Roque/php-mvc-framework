<?php

namespace App\Controller\Pages;

use App\Model\Entity\Testimony as EntityTestimony;
use \App\Utils\Db_Mngr\Pagination;
use \App\Session\Security\CSRF as SessionCSRF;

// Extende Page para usar getpage e gerar uma redundancia.
class Testimony extends Page
{

   /**
    * 
    * Metodo responsavel por obter a renderizacao dos items de nota para a pagina
    *
    * @param Request $request
    * @param Pagination $obPagination
    * @return array 
    * 
    */

   private static function getTestemonyItems($request, &$obPagination)
   {

      $itens = [];

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

      foreach ($results as $obTestimony) {

         $itens[] = $obTestimony->toArray();
      }

      return $itens;
   }

   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    * @return string
    * 
    */

   public static function getTestimonies($request)
   {

      // Retorna a view da pagina
      return parent::getPage(title: 'Testimonies', view: 'pages/testimonies.html', vars: [
         'testimonies' => self::getTestemonyItems($request, $obPagination),
         'paginations' => parent::getPagination($request, $obPagination)
      ]);
   }

   public static function insertTestimony($request)
   {
      // dados do post
      $postVars = $request->getPostVars();

      if (isset($postVars['enviar'])) {

         // CSRF
         $token = isset($postVars['csrf_token']) ? $postVars['csrf_token'] : '';

         if (!SessionCSRF::validateToken(form_token: $token)) {
            die('Token CSRF inválido.');
         }

         $obTestimony = new EntityTestimony(
            nome: $postVars['nome'],
            mensagem: $postVars['mensagem']
         );

         $obTestimony->cadastrar();
      }

      $request->getRouter()->redirect('/depoimentos');
   }
}

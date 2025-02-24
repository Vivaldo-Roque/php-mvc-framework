<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page
{


   /**
    * 
    * Método responsável por injectar html
    * @return string
    * 
    */

   private static function getHtmlFile($htmlFileName)
   {
      return View::render('pages/' . $htmlFileName);
   }

   /**
    * Metodo responsavel por retornar um link da paginacao
    * @param array $queryParams
    * @param array $page
    * @param string $url
    * @return string
    */
    private static function getPaginationLink($queryParams, $page, $url, $label = null){
      // Altera a pagina
      $queryParams['page'] = $page['page'];

      // Gerar o link
      $link = $url . '?' . http_build_query($queryParams);

      // Verificar se a pagina é mesmo a atual
      $activePage = $page['current'] ? 'active' : '';

      // Renderizacao da view
      return View::render('pages/pagination/link', [
         'page' => $label ?? $page['page'],
         'link' => $link,
         'active' => $activePage
      ]);
    }

   /**
    * 
    * Método responsável por renderizar o layout de paginacao
    * @param Request $request
    * @param Pagination $obPagination
    * @return string
    * 
    */
   public static function getPagination($request, $obPagination)
   {

      // Paginas
      $pages = $obPagination->getPages();

      // Verificar se tem mais de 1 pagina
      if (count($pages) <= 1) {
         return '';
      }

      // Links
      $links = '';

      // url atual sem gets
      $url = $request->getRouter()->getCurrentUrl();

      // Valores de get
      $queryParams = $request->getQueryParams();

      // Pagina atual
      $currentPage = $queryParams['page'] ?? 1;

      // Limite de paginas
      $limit = getenv('PAGINATION_LIMIT');

      // Meio da paginacao
      $middle = ceil($limit/2);

      // Inicio da paginacao
      $start = $middle > $currentPage ? 0 : $currentPage - $middle;

      // Ajusta o final da paginacao
      $limit = $limit + $start;

      // Ajusta o inicio da paginacao
      if($limit > count($pages)){
         $diff = $limit - count($pages);
         $start = $start - $diff;
      }

      // Link inicial
      if($start > 0){
         $links .= self::getPaginationLink($queryParams, reset($pages), $url, '<<');
      }

      // Renderiza os links
      foreach ($pages as $page) {

         // Verifica o start da paginacao
         if($page['page'] <= $start){
            continue;
         }

         // Verifica o limite de paginacao
         if($page['page'] > $limit){
            $links .= self::getPaginationLink($queryParams, end($pages), $url, '>>');
            break;
         }

         $links .= self::getPaginationLink($queryParams, $page, $url);
      }

      // Renderiza box de paginacao
      return View::render('pages/pagination/box', [
         'links' => $links
      ]);
      
   }


   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    * @return string
    * 
    */

   public static function getPage($title, $content)
   {
      return View::render('pages/page', [
         'title' => $title,
         'header' => self::getHtmlFile('header'),
         'content' => $content,
         'footer' => self::getHtmlFile('footer'),
         'scripts' => self::getHtmlFile('scripts'),
         'styles' => self::getHtmlFile('styles'),
         'URL' => URL,
      ]);
   }
}

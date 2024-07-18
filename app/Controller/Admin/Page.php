<?php

namespace App\Controller\Admin;

use App\Utils\Debug;
use \App\Utils\View;

class Page
{

   /**
    * 
    * Modulos disponiveis no painel
    * @var array
    * 
    */
   private static $modules = [
      'home' => [
         'label' => 'Home',
         'link' => URL . '/admin'
      ],
      'testimonies' => [
         'label' => 'Depoimentos',
         'link' => URL . '/admin/testimonies'
      ],
      'users' => [
         'label' => 'Usuarios',
         'link' => URL . '/admin/users'
      ]
   ];


   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    * @param string $title
    * @param string $view
    * @param array $vars
    * @return string
    * 
    */

   public static function getPage($title, $view, $vars = [])
   {
      $vars['title'] = $title;
      $vars['URL'] = URL;

      return View::render($view, $vars);
   }

   /**
    * 
    * Método responsável por renderizar a view do menu do painel
    *
    * @param string $currentModule
    * @return string
    * 
    */
   private static function getMenu($currentModule)
   {

      // Links fo menu
      $links = [];

      // itera os modulos
      foreach (self::$modules as $hash => $module) {
         $links[] = [
            'label' => $module['label'],
            'link' => $module['link'],
            'current' => $hash == $currentModule ? 'text-danger' : ''
         ];
      }

      // Retorna a renderizacao do menu
      return $links;
   }

   /**
    * 
    * Método responsável por renderizar a view do painel com conteudos dinamicos
    *
    * @param string $title
    * @param string $content
    * @param string $currentModule
    * @return string
    * 
    */
   public static function getPanel($title, $view, $vars = [], $currentModule)
   {

      $vars['menus'] = self::getMenu($currentModule);

      // Retorna a pagina renderizada
      return self::getPage(title: $title, view: $view, vars: $vars);
   }

   /**
    * Metodo responsavel por retornar um link da paginacao
    * @param array $queryParams
    * @param array $page
    * @param string $url
    * @return string
    */
   private static function getPaginationLink($queryParams, $page, $url, $label = null)
   {
      // Altera a pagina
      $queryParams['page'] = $page['page'];

      // Gerar o link
      $link = $url . '?' . http_build_query($queryParams);

      // Verificar se a pagina é mesmo a atual
      $activePage = $page['current'] ? 'active' : '';

      // Renderizacao da view
      return [
         'page' => $label ?? $page['page'],
         'link' => $link,
         'active' => $activePage
      ];
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

      // Debug::print($pages);

      // Verificar se tem mais de 1 pagina
      if (count($pages) <= 1) {
         return '';
      }

      // Links
      $links = [];

      // url atual sem gets
      $url = $request->getRouter()->getCurrentUrl();

      // Valores de get
      $queryParams = $request->getQueryParams();

      // Pagina atual
      $currentPage = $queryParams['page'] ?? 1;

      // Limite de paginas
      $limit = getenv('PAGINATION_LIMIT');

      // Meio da paginacao
      $middle = ceil($limit / 2);

      // Inicio da paginacao
      $start = $middle > $currentPage ? 0 : $currentPage - $middle;

      // Ajusta o final da paginacao
      $limit = $limit + $start;

      // Ajusta o inicio da paginacao
      if ($limit > count($pages)) {
         $diff = $limit - count($pages);
         $start = $start - $diff;
      }

      // Link inicial
      if ($start > 0) {
         $links[] = self::getPaginationLink($queryParams, reset($pages), $url, '<<');
      }

      // Renderiza os links
      foreach ($pages as $page) {

         // Verifica o start da paginacao
         if ($page['page'] <= $start) {
            continue;
         }

         // Verifica o limite de paginacao
         if ($page['page'] > $limit) {
            $links[] = self::getPaginationLink($queryParams, end($pages), $url, '>>');
            break;
         }

         $links[] = self::getPaginationLink($queryParams, $page, $url);
      }

      // retorna os links
      return $links;
   }
}

<?php

namespace App\Controller\Admin;

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
         'link' => URL.'/admin'
      ],
      'testimonies' => [
         'label' => 'Depoimentos',
         'link' => URL.'/admin/testimonies'
      ],
      'users' => [
         'label' => 'Usuarios',
         'link' => URL.'/admin/users'
      ]
   ];


   /**
    * 
    * Método responsável por injectar html
    * @return string
    * 
    */
   private static function getHtmlFile($htmlFileName)
   {
      return View::render('admin/' . $htmlFileName);
   }


   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    *
    * @param string $title
    * @param string $content
    * @return string
    * 
    */
   public static function getPage($title, $content)
   {
      return View::render('admin/page', [
         'title' => $title,
         'content' => $content,
         'scripts' => self::getHtmlFile('scripts'),
         'styles' => self::getHtmlFile('styles'),
      ]);
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
      $links = '';

      // itera os modulos
      foreach(self::$modules as $hash=>$module){
         $links .= View::render('admin/menu/link', [
            'label' => $module['label'],
            'link' => $module['link'],
            'current' => $hash == $currentModule ? 'text-danger' : ''
         ]);
      }

      // Retorna a renderizacao do menu
      return View::render('admin/menu/box', [
         'links' => $links
      ]);
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
   public static function getPanel($title, $content, $currentModule)
   {

      // Renderiza a view do painel
      $contentPanel = View::render('admin/panel', [
         'menu' => self::getMenu($currentModule),
         'content' => $content
      ]);

      // Retorna a pagina renderizada
      return self::getPage($title, $contentPanel);
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

      // Renderiza os links
      foreach ($pages as $page) {

         // Altera a pagina
         $queryParams['page'] = $page['page'];

         // Gerar o link
         $link = $url . '?' . http_build_query($queryParams);

         // Verificar se a pagina é mesmo a atual
         $activePage = $page['current'] ? 'active' : '';

         // Renderizacao da view
         $links .= View::render('admin/pagination/link', [
            'page' => $page['page'],
            'link' => $link,
            'active' => $activePage
         ]);
      }

      // Renderiza box de paginacao
      return View::render('admin/pagination/box', [
         'links' => $links
      ]);
      
   }
}

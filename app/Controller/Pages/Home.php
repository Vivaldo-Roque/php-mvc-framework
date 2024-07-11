<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Home extends Page
{

   // Extende Page para usar getpage e gerar uma redundancia.

   /**
    * 
    * Método responsável por retornar o conteúdo {view} da nossa home
    * @return string
    * 
    */

   public static function getHome($request)
   {

      // View da home
      $content = View::render('pages/home', []);

      // Retorna a view da pagina
      return parent::getPage('Home', $content);
   }

}

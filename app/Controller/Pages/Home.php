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
      // Retorna a view da pagina
      return parent::getPage(title: 'Home', view: 'pages/home.html', vars: []);
   }

}

<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class About extends Page {

    // Extende Page para usar getpage e gerar uma redundancia.

    /**
     * 
     * Método responsável por retornar o conteúdo {view} da nossa about
     * @return string
     * 
     */

     public static function getAbout ($request){

        // Retorna a view da pagina
        return parent::getPage(title: 'About', view: 'pages/about.html', vars: []);
     }

}
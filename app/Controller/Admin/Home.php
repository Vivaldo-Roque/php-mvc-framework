<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Home extends Page
{

    /**
     * 
     * Metodo responsavel por retornar a renderizacao da pagina de home admin
     * @param Request $request
     * @param string $errormessage
     * @return string
     * 
     */

    public static function getHome($request)
    {
        // Conteudo da home
        $content = view::render('admin/modules/home/index',[]);

        // Retorna a pagina completa
        return parent::getPanel('Home', $content, 'home');
    }

    

}

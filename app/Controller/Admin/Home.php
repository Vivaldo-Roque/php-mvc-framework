<?php

namespace App\Controller\Admin;

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
        // Retorna a pagina completa
        return parent::getPanel(title: 'Home', view: 'admin/modules/home/index.html', vars: [], currentModule: 'home');
    }

    

}

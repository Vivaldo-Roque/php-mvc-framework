<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert
{

    /**
     * 
     * Metodo responsavel por retornar uma mensagem de sucesso
     * @param string $message
     * @return string
     * 
     */

    public static function getSuccess($message)
    {
        return View::render(
            'admin/alert/status',
            [
                'tipo' => 'success',
                'mensagem' => $message
            ]);
    }

    /**
     * 
     * Metodo responsavel por retornar uma mensagem de erro
     * @param string $message
     * @return string
     * 
     */

     public static function getError($message)
     {
         return View::render(
             'admin/alert/status',
             [
                 'tipo' => 'danger',
                 'mensagem' => $message
             ]);
     }
}

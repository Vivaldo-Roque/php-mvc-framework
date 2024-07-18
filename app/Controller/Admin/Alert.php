<?php

namespace App\Controller\Admin;

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
        return [
            'tipo' => 'success',
            'mensagem' => $message
        ];
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
        return [
            'tipo' => 'danger',
            'mensagem' => $message
        ];
    }
}

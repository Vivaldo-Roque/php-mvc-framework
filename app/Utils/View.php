<?php

namespace App\Utils;

use \App\Session\Security\CSRF as SessionCSRF;

class View {

    /**
     * 
     * Variavel responsável por guardar o conteúdo de uma view
     * 
     * @var array $vars
     */

     private static $vars = [];

    /**
     * 
     * Método responsável por definir os dados iniciais da classe
     * 
     * @param array $vars
     */

    public static function init($vars = []) {
        self::$vars = $vars;
    }

    /**
     * 
     * Método responsável por retornar o conteúdo de uma view
     * 
     * @param string $view
     * @return TemplateWrapper
     */

     public static function getContentView($view){
        return TwigSingleton::getInstance()->getTwig()->load($view);
     }

    public static function render($view, $vars = []){
        // Conteúdo da view
        $contentView = self::getContentView($view);

        // Unir as variaveis da classe com dos controladores
        $vars = array_merge(self::$vars, $vars);

        // Twig render
        $content = $contentView->render($vars);

        // Retorna conteúdo renderizado
        return $content;
    }

}
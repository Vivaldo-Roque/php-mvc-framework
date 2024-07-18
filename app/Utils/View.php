<?php

namespace App\Utils;

class View {

    /**
     * 
     * Variavel responsável por guardar o local dos templates
     * 
     * @var string $template_dir
     */

     private static $template_dir;

    /**
     * 
     * Variavel responsável por guardar o conteúdo de uma view
     * 
     * @var array $vars
     */

     private static $vars = [];

     /**
     * 
     * Variavel responsável por guardar a instancia de twig
     * 
     * @var \Twig\Environment $twig
     */

     private static $twig;

    /**
     * 
     * Método responsável por definir os dados iniciais da classe
     * 
     * @param array $vars
     */

    public static function init($template_dir, $vars = []) {
        self::$template_dir = $template_dir;
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
        $loader = new \Twig\Loader\FilesystemLoader(self::$template_dir);
        self::$twig = new \Twig\Environment($loader);
        return self::$twig->load($view);
     }

    public static function render($view, $vars = []){
        
        // Conteúdo da view
        $contentView = self::getContentView($view);

        // Unir as variaveis da classe com dos controladores
        $vars = array_merge(self::$vars, $vars);

        $content = $contentView->render($vars);

        // Retorna conteúdo renderizado
        return $content;
    }

}
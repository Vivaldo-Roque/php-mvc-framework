<?php

namespace App\Utils;

class View {

    /**
     * 
     * Método responsável por retornar o conteúdo de uma view
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
     * @return string
     */

     public static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
     }


    /**
     * 
     * Método responsável por retornar o conteúdo renderizado de uma view
     * 
     * @param string $view
     * @param array $vars (string/numeric)
     * @return string
     */

    private static function removeEmptyVars($string) {
        return preg_replace('/{{[^}]+}}/', '', $string);
    }

    public static function render($view, $vars = []){
        
        // Conteúdo da view
        $contentView = self::getContentView($view);

        // Unir as variaveis da classe com dos controladores
        $vars = array_merge(self::$vars, $vars);

        // Chaves do array de variaveis
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        
        // renderiza o conteudo
        $content = str_replace($keys, array_values($vars), $contentView);
        
        // Remove variaveis não definidas no HTML
        $content = self::removeEmptyVars($content);

        // Retorna conteúdo renderizado
        return $content;
    }

}
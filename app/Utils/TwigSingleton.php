<?php

namespace App\Utils;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class TwigSingleton
{

     /**
     * 
     * Variavel responsável por guardar a instancia de twig
     * 
     * @var Environment $twig
     */

     private Environment $twig;

    /**
     * Singleton Instance
     *
     * @var TwigSingleton
     */
    private static $instance;

    /**
     * Private Constructor
     *
     * We can't use the constructor to create an instance of the class
     *
     * @return void
     */
    private function __construct()
    {

    }

    public static function init($template_dir){
        // Init twig
        $loader = new FilesystemLoader($template_dir);
        self::getInstance()->twig = new Environment($loader);
    }

    public function getTwig(){
        return $this->twig;
    }

    /**
     * Get the singleton instance
     *
     * @return TwigSingleton
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
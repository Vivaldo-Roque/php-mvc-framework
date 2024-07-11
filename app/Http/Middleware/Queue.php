<?php

namespace App\Http\Middleware;

class Queue {

    /**
     * 
     * Mapeamemto de middlewares
     * @var array
     */
     private static $map = [];

     /**
     * 
     * Mapeamemto de middlewares que serao carregados em todas as rotas
     * @var array
     */
    private static $defaultMiddlewares = [];

    /**
     * 
     * Fila de middlewares a serem executados
     * @var array
     */
    private $middlewares = [];

    /**
     * 
     * Funcao de execucao do controlador
     * @var callable
     */
    private $controller;

    /**
     * 
     * Argumentos da funcao do controller
     * @var array
     */
    private $controllerArgs = [];


    /**
     * 
     * Metodo responsavel por construir a classe de fila de middlewares
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$defaultMiddlewares, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * 
     * Metodo responsavel por definir o mapeamento de middlewares
     * @param array $map
     */

     public static function setMap($map){
        self::$map = $map;
     }

     /**
     * 
     * Metodo responsavel por definir o mapeamento de middlewares padores
     * @param array $map
     */

     public static function setDefaultMiddlewares($defaultMiddlewares){
        self::$defaultMiddlewares = $defaultMiddlewares;
     }

    /**
     * 
     * Metodo responsavel por executar o proximo nivel da fila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request){
        // Verifica se a fila esta vazia
        if(empty($this->middlewares)){
            return call_user_func_array($this->controller, $this->controllerArgs);
        }

        // Middleware
        $middleware = array_shift($this->middlewares);

        // Verifica o mapeamento
        if(!isset(self::$map[$middleware])){
            throw new \Exception("Problemas ao processar o middleware da requisição", 500);
        }

        // NEXT
        $queue = $this;
        // chama ela mesma
        $next = function($request) use ($queue){
            return $queue->next($request);
        };

        // Executa o middleware
        return (new self::$map[$middleware])->handle($request, $next);
    }
}
<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;
use App\Utils\Debug;
use App\Utils\View;

class Router
{


   /**
    * 
    * 
    * Urm completa do projeto (raiz)
    * 
    * @var string
    * 
    */


   private $url = '';

   /**
    * 
    * 
    * Prefixo de todas as rotas
    * 
    * @var string
    * 
    */

   private $prefix = '';

   /**
    * 
    * 
    * indice de routas
    * 
    * @var array
    * 
    */

   private $routes = [];

   /**
    * 
    * 
    * instancia de request
    * 
    * @var Request
    * 
    */

   private $request;

   /**
    * 
    * 
    * ContentType padrao do response
    * 
    * @var string
    * 
    */

    private $contentType = 'text/html';

    /**
    * 
    * 
    * Metodo responsavel por alterar o valor do contentType
    * 
    * @param string
    * 
    */
    public function setContentType($contentType){
      if(isset($contentType)){
         $this->contentType = $contentType;
      }
    }

   /**
    * 
    * 
    * Metodo responsavel por iniciar a classe
    * 
    * @param string
    * 
    */

   public function __construct($url)
   {
      $this->request = new Request($this);
      $this->url = $url;
      $this->setPrefix();
   }

   /**
    * 
    * Metodo responsavel por definir o prefixo das rotas
    * 
    * @var string
    * 
    */
   public function setPrefix()
   {

      // informacoes da url atual
      $parseUrl = parse_url($this->url);

      // define o prefixo
      $this->prefix = $parseUrl['path'] ?? '';
   }

   /**
    * 
    * 
    * Metodo responsavel por adicionar uma rota na classe
    * 
    * @param string $method
    * @param string $route
    * @param array $params
    * 
    */

   private function addRoute($method, $route, $params = [])
   {

      // Validacao dos parametros
      foreach ($params as $key => $value) {
         if ($value instanceof Closure) {
            $params['controller'] = $value;
            unset($params[$key]);
            continue;
         }
      }

      // Middlewares da rota
      $params['middlewares'] = $params['middlewares'] ?? [];

      // variaveis da rota
      $params['variables'] = [];

      // padrao de validacao das variaveis das rotas
      $patterVariable = '/{(.*?)}/';
      if (preg_match_all($patterVariable, $route, $matches)) {
         $route = preg_replace($patterVariable, '(.*?)', $route);
         $params['variables'] = $matches[1];
      }

      // padrao de validacao da url
      $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

      // Adiciona a rota dentro da classe
      $this->routes[$patternRoute][$method] = $params;

   }

   /**
    * 
    * 
    * metodo responsavel por definir uma rota de GET
    * 
    * @param string $route
    * @param array $params
    * 
    */

   public function get($route, $params = [])
   {
      return $this->addRoute('GET', $route, $params);
   }

   /**
    * 
    * 
    * metodo responsavel por definir uma rota de POST
    * 
    * @param string $route
    * @param array $params
    * 
    */

   public function post($route, $params = [])
   {
      return $this->addRoute('POST', $route, $params);
   }

   /**
    * 
    * 
    * metodo responsavel por definir uma rota de PUT
    * 
    * @param string $route
    * @param array $params
    * 
    */

   public function put($route, $params = [])
   {
      return $this->addRoute('PUT', $route, $params);
   }

   /**
    * 
    * 
    * metodo responsavel por definir uma rota de DELETE
    * 
    * @param string $route
    * @param array $params
    * 
    */

   public function delete($route, $params = [])
   {
      return $this->addRoute('DELETE', $route, $params);
   }

   /**
    * 
    * 
    * Metodo responsavel por retornar a uri desconsiderando o prefixo
    * 
    * @return string
    * 
    */

   public function getUri()
   {
      // uri da request
      $uri = $this->request->getUri();

      // Fatia a uri com o prefixo
      $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

      $res = end($xUri);
      // Remove a barra no final da uri caso tenha
      if(strlen($res) > 1 && str_ends_with( $res , "/")){
         $res = rtrim($res, "/");
      }

      // Retorna a uri sem prefixo
      return $res;
   }


   /**
    * 
    * 
    * Metodo responsavel por retornar os dados da rota atual
    * 
    * @return array
    * 
    */

   private function getRoute()
   {
      // uri
      $uri = $this->getUri();

      // metodo
      $httpMethod = $this->request->getHttpMethod();

      // Valida as rotas
      foreach ($this->routes as $patternRoute => $methods) {
         // Verifica se a uri bate com o padrao
         if (preg_match($patternRoute, $uri, $matches)) {
            // Verificar o metodo
            if (isset($methods[$httpMethod])) {
               
               // Remover o nao necessario
               unset($matches[0]);

               // chaves
               $keys = $methods[$httpMethod]['variables'];
               $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
               $methods[$httpMethod]['variables']['request'] = $this->request;

               // retorno dos parametros
               return $methods[$httpMethod];
            }

            // metodo nao definido/permitido
            throw new Exception(View::render("pages/default_error/405"), 405);
         }
      }
      // Renderiza a pagina 404 e retorna uma exception com o codigo
      throw new Exception(View::render("pages/default_error/404"), 404);
   }

   public function run()
   {
      try {

         // obtem a rota atual
         $route = $this->getRoute();

         // Verificar o controlador
         if (!isset($route['controller'])) {
            // Renderiza a pagina 404 e retorna uma exception com o codigo
            throw new Exception(View::render("pages/default_error/500"), 500);
         }

         // argumentos da funcao
         $args = [];

         // Reflection
         $reflection = new ReflectionFunction($route['controller']);
         foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[$name] = $route['variables'][$name] ?? '';
         }

         // retorna a execucao da fila de middlewares
         $obMiddlewareQueue = new MiddlewareQueue($route['middlewares'], $route['controller'], $args);
         return $obMiddlewareQueue->next($this->request);
      } catch (Exception $e) {
         return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
      }
   }

   /**
    * 
    * Metodo responsavel por retornar a mensagem de erro de acordo o contentType
    * @param string $message
    * @return string
    * 
    */

    private function getErrorMessage($message){
      switch($this->contentType){
         case 'application/json':
            return [
               'error' => $message
            ];
            break;
         case 'text/html':
            return $message;
            break;
      }
    }

   /**
    * 
    * 
    * Metodo responsavel por retornar a url atual (sem gets)
    * 
    * @return string
    * 
    */

   public function getCurrentUrl()
   {
      return $this->url . $this->getUri();
   }

   /**
    * 
    * Metodo responsavel por redirecionar a URL
    * @param string $route
    *
    */

   public function redirect($route)
   {
      // URL
      $url = $this->url . $route;

      // Executa o redirect
      header('location: ' . $url);
      exit;
   }
}

<?php


namespace App\Http;

class Request
{

   /**
    * 
    * Instancia do router
    * @var Router
    * 
    */

   private $router;

   /**
    * 
    * Metodo HTTP da requisicao
    * @var string
    * 
    */

   private $httpMethod;

   /**
    * 
    * URI da pagina
    * @var string
    * 
    */

   private $uri;

   /**
    * 
    * Parametros da URL ($_GET)
    * @var array
    * 
    */

   private $queryParams = [];

   /**
    * 
    * Variaveis recebidas no POST da pagina ($_POST)
    * @var array
    * 
    */

   private $postVars = [];

   /**
    * 
    * Cabecalho da requisicao
    * @var array
    * 
    */

   private $headers = [];

   /**
    * 
    * Variavel que guarda o usuario da HTTP BASIC AUTH
    * @var User
    * 
    */

   private $user;

   /**
    * 
    * Definir getters e setters
    * 
    */

   private function setUser($user)
   {
      if (isset($user)) {
         $this->user = $user;
      }
   }

   private function getUser()
   {
      return $this->user;
   }

   /**
    * 
    * PHP metodos magicos
    * 
    */

   public function __set($name, $value)
   {
      switch ($name) {
         case 'user':
            return $this->setUser($value);
      }
   }

   public function __get($name)
   {
      switch ($name) {
         case 'user':
            return $this->getUser();
      }
   }

   /**
    * 
    * Construtor da classe
    * 
    */

   public function __construct($router)
   {
      $this->router = $router;
      $this->queryParams = $_GET ?? [];
      $this->headers = getallheaders();
      $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
      $this->setUri();
      $this->setPostVars();
   }

   /**
    * 
    * Metodo responsavel por definir as variaveis do POST
    * 
    */
   private function setPostVars()
   {
      // Verifica o metodo da requisicao
      if ($this->httpMethod == 'GET') {
         return false;
      }

      // POST padrao
      $this->postVars = $_POST ?? [];

      // POST json
      $inputRaw = file_get_contents('php://input');
      if (strlen($inputRaw) && empty($_POST)) {
         $this->postVars = json_decode($inputRaw, true);
      }
   }

   /**
    * 
    * Metodo responsavel por definir a uri
    * 
    */
   private function setUri()
   {

      // uri com gets
      $this->uri = $_SERVER['REQUEST_URI'] ?? '';

      // Remove gets da uri
      $xURI = explode('?', $this->uri);
      $this->uri = $xURI[0];
   }


   /**
    * 
    * Metodo responsavel por retornar a instancia de router
    * @var Router
    * 
    */
   public function getRouter()
   {
      return $this->router;
   }

   /**
    * 
    * Metodo responsavel por retornar o metodo HTTP da requisicao
    * @var string
    * 
    */
   public function getHttpMethod()
   {
      return $this->httpMethod;
   }

   /**
    * 
    * Metodo responsavel por retornar a URI da requisicao
    * @var string
    * 
    */
   public function getUri()
   {
      return $this->uri;
   }

   /**
    * 
    * Metodo responsavel por retornar os Headers da requisicao
    * @var array
    * 
    */
   public function getHeaders()
   {
      return $this->headers;
   }

   /**
    * 
    * Metodo responsavel por retornar os parametros (GET) da URL da requisicao
    * @var array
    * 
    */
   public function getQueryParams()
   {
      return $this->queryParams;
   }

   /**
    * 
    * Metodo responsavel por retornar as variaveis Post da requisicao
    * @var array
    * 
    */
   public function getPostVars()
   {
      return $this->postVars;
   }
}

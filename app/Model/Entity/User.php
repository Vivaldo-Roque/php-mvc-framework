<?php

namespace App\Model\Entity;

use App\Utils\Db_Mngr\Database;
use App\Utils\Debug;
use \PDO;

class User
{

    /**
     * 
     * ID do usuario
     * 
     * @var integer
     * 
     */
    private $id;

    /**
     * 
     * Nome do usuario
     * 
     * @var string
     * 
     */
    private $nome;

    /**
     * 
     * Email do usuario
     * 
     * @var string
     * 
     */
    private $email;

    /**
     * 
     * Senha do usuario
     * 
     * @var string
     * 
     */
    private $senha;

    private static string $tableName = 'usuarios';

    /**
     * 
     * Definir getters e setters
     * 
     */

     private function setId($id)
    {
        if (isset($id) && is_numeric($id)) {
            $this->id = $id;
        }
    }

    private function getId()
    {
        return $this->id;
    }

    private function setNome($nome)
    {
        if (isset($nome) && !empty($nome)) {
            $this->nome = $nome;
        }
    }

    private function getNome()
    {
        return $this->nome;
    }

    private function setEmail($email)
    {
        if (isset($email) && !empty($email)) {
            $this->email = $email;
        }
    }

    private function getEmail()
    {
        return $this->email;
    }

    private function setSenha($senha)
    {
        if (isset($senha) && !empty($senha)) {
            $this->senha = password_hash($senha, PASSWORD_BCRYPT);
        }
    }

    private function getSenha()
    {
        return $this->senha;
    }

    /**
     * 
     * PHP metodos magicos
     * 
     */

    public function __set($name, $value)
    {
        switch ($name) {
            case 'id':
                return $this->setId($value);
            case 'nome':
                return $this->setNome($value);
            case 'email':
                return $this->setEmail($value);
            case 'senha':
                return $this->setSenha($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
            case 'nome':
                return $this->getNome();
            case 'email':
                return $this->getEmail();
            case 'senha':
                return $this->getSenha();
        }
    }

    public function __construct(int $id = null, string $nome = null, string $email = null, string $senha = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setSenha($senha);
    }

    /**
     * 
     * Metodo responsavel por definir os atributos da classe
     * @param integer $id
     * @param string $nome
     * @param string $email
     * @param string $senha
     * 
     */
    public function setAttrs(int $id = null, string $nome = null, string $email = null, string $senha = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setSenha($senha);
    }

    /**
     * 
     * Metodo responsavel por retornar o usuario com base em seu email
     * @param string $email
     * @return Usuario
     * 
     */
    public static function getUserByEmail($email)
    {

        $db = new Database(self::$tableName);

        $stmt = $db->select(where: "email='{$email}'");

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);

        $result = $stmt->fetch();

        return $result;
    }

    /**
     * 
     * Metodo responsavel por cadastrar a instancia atual no banco de dados
     * @return boolean
     */

     public function cadastrar()
     {
 
         $db = new Database(self::$tableName);
 
         // Insere o usuario no banco de dados
         $this->id = $db->insert([
             'nome' => $this->nome,
             'email' => $this->email,
             'senha' => $this->senha
         ]);
 
         // Sucesso
         return true;
     }

    /**
     * 
     * Metodo responsavel por atualizar os dados no banco de dados com a instancia atual
     * @return boolean
     */

    public function atualizar()
    {

        $db = new Database(self::$tableName);

        // Atualiza o usuario no banco de dados
        $result = $db->update("id = {$this->id}", [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        return $result;
    }

    /**
     * 
     * Metodo responsavel por excluir um registo no banco de dados usando ID
     * @return boolean
     */

    public function excluir()
    {

        $db = new Database(self::$tableName);

        // Excluir o usuario no banco de dados
        $result = $db->delete("id = {$this->id}");

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar o total de usuarios no banco de dados
     * @return integer
     */
    public static function countAll(): int
    {

        $db = new Database(self::$tableName);

        $result = (int)$db->select(fields: 'COUNT(*) as qtd')->fetchObject(self::class)->qtd;

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar o usuario com base no seu ID no banco de dados
     * @param integer $id
     * @return User
     */
    public static function getUserById($id)
    {

        $db = new Database(self::$tableName);

        $stmt = $db->select(where: "id = {$id}");

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, SELF::class);

        $result = $stmt->fetch();

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar todos os usuarios no banco de dados
     * @return array
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {

        $db = new Database(self::$tableName);

        $stmt = $db->select(order: 'id DESC', limit: $limit);

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, SELF::class);

        $results = $stmt->fetchAll();

        return $results;
    }
}

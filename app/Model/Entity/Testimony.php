<?php

namespace App\Model\Entity;

use App\Utils\Db_Mngr\Database;
use App\Utils\Debug;
use \PDO;

class Testimony
{

    /**
     * 
     * ID do depoimento
     * 
     * @var integer
     * 
     */
    private $id;

    /**
     * 
     * Nome do usuario que fez o depoimento
     * 
     * @var string
     * 
     */
    private $nome;

    /**
     * 
     * Mensagem do depoimento
     * 
     * @var string
     * 
     */
    private $mensagem;

    /**
     * 
     * Data de publicacao do depoimento
     * 
     * @var integer
     * 
     */
    private $data;

    private static string $tableName = 'depoimentos';

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

    private function setMensagem($mensagem)
    {
        if (isset($mensagem) && !empty($mensagem)) {
            $this->mensagem = $mensagem;
        }
    }

    private function getMensagem()
    {
        return $this->mensagem;
    }

    private function setData($data)
    {
        if (isset($data) && !empty($data)) {
            $this->data = $data;
        }
    }

    private function getData()
    {
        return date('d/m/Y H:i:s', strtotime($this->data));
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
            case 'mensagem':
                return $this->setMensagem($value);
            case 'data':
                return $this->setData($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
            case 'nome':
                return $this->getNome();
            case 'mensagem':
                return $this->getMensagem();
            case 'data':
                return $this->getData();
        }
    }


    public function __construct(int $id = null, string $nome = null, string $mensagem = null, string $data = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setMensagem($mensagem);
        $this->setData($data);
    }

    public function setAttrs(int $id = null, string $nome = null, string $mensagem = null, string $data = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setMensagem($mensagem);
        $this->setData($data);
    }

    /**
     * 
     * Metodo responsavel por retornar um array da classe
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'mensagem' => $this->getMensagem(),
            'data' => $this->getData()
        ];
    }

    /**
     * 
     * Metodo responsavel por cadastrar a instancia atual no banco de dados
     * @return boolean
     */

    public function cadastrar()
    {

        $db = new Database(self::$tableName);

        // Define a data
        $this->data = date('Y-m-d H:i:s');

        // Insere o depoimento no banco de dados
        $this->id = $db->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

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

        // Atualiza o depoimento no banco de dados
        $result = $db->update("id = {$this->id}", [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
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

        // Excluir o depoimento no banco de dados
        $result = $db->delete("id = {$this->id}");

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar o total de depoimentos no banco de dados
     * @return integer
     */
    public static function countAll(): int
    {

        $db = new Database(self::$tableName);

        $result = (int)$db->select(fields: 'COUNT(*) as qtd')->fetchObject()->qtd;

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar o depoimento com base no seu ID no banco de dados
     * @param integer $id
     * @return Testimony
     */
    public static function getTestimonyById($id)
    {

        $db = new Database(self::$tableName);

        $stmt = $db->select(where: "id = {$id}");

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, SELF::class);

        $result = $stmt->fetch();

        return $result;
    }

    /**
     * 
     * Metodo responsavel por retornar todos os depoimentos no banco de dados
     * @return array
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
    {

        $db = new Database(self::$tableName);

        $stmt = $db->select(order: 'id DESC', limit: $limit);

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, SELF::class);

        $results = $stmt->fetchAll();

        return $results;
    }
}

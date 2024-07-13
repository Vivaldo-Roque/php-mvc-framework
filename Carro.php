<?php

namespace App\Model\Entity;

use App\Utils\Db_Mngr\Database;
use PDO;

class Carro
{

    /**
     * Nome da tabela
     * 
     * @var string
     */
    private static string $tableName = 'carros';

    /**
     * 
     * @var int
     */
    private int $id;

    /**
     * 
     * @var string
     */
    private string $nome;

    /**
     * 
     * @var string
     */
    private string $cor;

    /**
     * 
     * @var float
     */
    private float $kilometragem;

    private function setId(int $value)
    {
        if (isset($value) && is_numeric($value)) {
            $this->id = $value;
        }
    }

    private function getId(): int
    {
        return $this->id;
    }

    private function setNome(string $value)
    {
        if (isset($value) && !empty($value)) {
            $this->nome = $value;
        }
    }

    private function getNome(): string
    {
        return $this->nome;
    }

    private function setCor(string $value)
    {
        if (isset($value) && !empty($value)) {
            $this->cor = $value;
        }
    }

    private function getCor(): string
    {
        return $this->cor;
    }

    private function setKilometragem(float $value)
    {
        if (isset($value) && is_numeric($value)) {
            $this->kilometragem = $value;
        }
    }

    private function getKilometragem(): float
    {
        return $this->kilometragem;
    }

    public function __construct(int $id = null, string $nome = null, string $cor = null, float $kilometragem = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setCor($cor);
        $this->setKilometragem($kilometragem);
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'id':
                return $this->setId($value);
            case 'nome':
                return $this->setNome($value);
            case 'cor':
                return $this->setCor($value);
            case 'kilometragem':
                return $this->setKilometragem($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
            case 'nome':
                return $this->getNome();
            case 'cor':
                return $this->getCor();
            case 'kilometragem':
                return $this->getKilometragem();
        }
    }

    public function setAttrs(int $id = null, string $nome = null, string $cor = null, float $kilometragem = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setCor($cor);
        $this->setKilometragem($kilometragem);
    }

    /**
     * Método responsável por retornar o Carro com base no ID
     * 
     * @param int $id
     * @return self
     */
    public static function getCarroById(int $id): self
    {
        $db = new Database(self::$tableName);
        $stmt = $db->select(where: "id = {$id}");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
        return $stmt->fetch();
    }

    /**
     * Método responsável por retornar o Carro com base no email
     * 
     * @param string $email
     * @return self
     */
    public static function getCarroByEmail(string $email): self
    {
        $db = new Database(self::$tableName);
        $stmt = $db->select(where: "email = '{$email}'");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
        return $stmt->fetch();
    }

    /**
     * Método responsável por salvar o Carro no banco de dados
     * 
     * @return bool
     */
    public function save(): bool
    {
        $db = new Database(self::$tableName);
        $this->id = $db->insert([
            'id' => $this->id,
            'nome' => $this->nome,
            'cor' => $this->cor,
            'kilometragem' => $this->kilometragem,
        ]);
        return true;
    }

    /**
     * Método responsável por atualizar o Carro no banco de dados
     * 
     * @return bool
     */
    public function update(): bool
    {
        $db = new Database(self::$tableName);
        return $db->update("id = {$this->id}", [
            'id' => $this->id,
            'nome' => $this->nome,
            'cor' => $this->cor,
            'kilometragem' => $this->kilometragem,
        ]);
    }

    /**
     * Método responsável por excluir o Carro no banco de dados
     * 
     * @return bool
     */
    public function delete(): bool
    {
        $db = new Database(self::$tableName);
        return $db->delete("id = {$this->id}");
    }

    /**
     * Método responsável por retornar o número total de Carro no banco de dados
     * 
     * @return int
     */
    public static function countAll(): int
    {
        $db = new Database(self::$tableName);
        return $db->select(fields: 'COUNT(*) as count')->fetchObject()->count;
    }

    /**
     * Método responsável por retornar todos os Carro no banco de dados
     * 
     * @return array
     */
    public static function getUsers(): array
    {
        $db = new Database(self::$tableName);
        return $db->select()->fetchAll(PDO::FETCH_CLASS, self::class);
    }

}

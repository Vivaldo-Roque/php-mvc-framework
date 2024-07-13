<?php

function generateClassFromFields($className, $inputFile, $outputFile, $tableName)
{
    // Mudar a primeira letra para maiúscula
    $className = ucfirst($className);

    // Ler os campos do arquivo de entrada
    $fields = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Inicializa o código da classe
    $classCode = "<?php\n\n";
    $classCode .= "namespace App\Model\Entity;\n\n";
    $classCode .= "use App\Utils\Db_Mngr\Database;\n";
    $classCode .= "use PDO;\n\n";
    $classCode .= "class " . $className . "\n{\n\n";

    // Adiciona o atributo de nome da tabela
    $classCode .= "    /**\n";
    $classCode .= "     * Nome da tabela\n";
    $classCode .= "     * \n";
    $classCode .= "     * @var string\n";
    $classCode .= "     */\n";
    $classCode .= "    private static string \$tableName = '{$tableName}';\n\n";

    // Adiciona os atributos privados
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $classCode .= "    /**\n";
        $classCode .= "     * \n";
        $classCode .= "     * @var {$type}\n";
        $classCode .= "     */\n";
        $classCode .= "    private {$type} \${$name};\n\n";
    }

    // Adiciona os getters e setters
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $capitalizedField = ucfirst($name);

        if ($type == 'int' || $type == 'float') {
            // Setter
            $classCode .= "    private function set{$capitalizedField}({$type} \$value)\n";
            $classCode .= "    {\n";
            $classCode .= "        if (isset(\$value) && is_numeric(\$value)) {\n";
            $classCode .= "            \$this->{$name} = \$value;\n";
            $classCode .= "        }\n";
            $classCode .= "    }\n\n";
        } else {
            // Setter
            $classCode .= "    private function set{$capitalizedField}({$type} \$value)\n";
            $classCode .= "    {\n";
            $classCode .= "        if (isset(\$value) && !empty(\$value)) {\n";
            $classCode .= "            \$this->{$name} = \$value;\n";
            $classCode .= "        }\n";
            $classCode .= "    }\n\n";
        }

        // Getter
        $classCode .= "    private function get{$capitalizedField}(): {$type}\n";
        $classCode .= "    {\n";
        $classCode .= "        return \$this->{$name};\n";
        $classCode .= "    }\n\n";
    }

    // Adiciona o construtor
    $classCode .= "    public function __construct(";
    $constructorParams = [];
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $constructorParams[] = "{$type} \${$name} = null";
    }
    $classCode .= implode(', ', $constructorParams);
    $classCode .= ")\n";
    $classCode .= "    {\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $classCode .= "        \$this->set" . ucfirst($name) . "(\${$name});\n";
    }
    $classCode .= "    }\n\n";

    // Adiciona os métodos mágicos __set e __get
    $classCode .= "    public function __set(\$name, \$value)\n";
    $classCode .= "    {\n";
    $classCode .= "        switch (\$name) {\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $capitalizedField = ucfirst($name);
        $classCode .= "            case '{$name}':\n";
        $classCode .= "                return \$this->set{$capitalizedField}(\$value);\n";
    }
    $classCode .= "        }\n";
    $classCode .= "    }\n\n";

    $classCode .= "    public function __get(\$name)\n";
    $classCode .= "    {\n";
    $classCode .= "        switch (\$name) {\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $capitalizedField = ucfirst($name);
        $classCode .= "            case '{$name}':\n";
        $classCode .= "                return \$this->get{$capitalizedField}();\n";
    }
    $classCode .= "        }\n";
    $classCode .= "    }\n\n";

    // Adiciona o método setAttrs
    $classCode .= "    public function setAttrs(";
    $setAttrsParams = [];
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $setAttrsParams[] = "{$type} \${$name} = null";
    }
    $classCode .= implode(', ', $setAttrsParams);
    $classCode .= ")\n";
    $classCode .= "    {\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $classCode .= "        \$this->set" . ucfirst($name) . "(\${$name});\n";
    }
    $classCode .= "    }\n\n";

    // Adiciona os métodos para interagir com o banco de dados
    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por retornar o {$className} com base no ID\n";
    $classCode .= "     * \n";
    $classCode .= "     * @param int \$id\n";
    $classCode .= "     * @return self\n";
    $classCode .= "     */\n";
    $classCode .= "    public static function get{$className}ById(int \$id): self\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        \$stmt = \$db->select(where: \"id = {\$id}\");\n";
    $classCode .= "        \$stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);\n";
    $classCode .= "        return \$stmt->fetch();\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por retornar o {$className} com base no email\n";
    $classCode .= "     * \n";
    $classCode .= "     * @param string \$email\n";
    $classCode .= "     * @return self\n";
    $classCode .= "     */\n";
    $classCode .= "    public static function get{$className}ByEmail(string \$email): self\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        \$stmt = \$db->select(where: \"email = '{\$email}'\");\n";
    $classCode .= "        \$stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);\n";
    $classCode .= "        return \$stmt->fetch();\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por salvar o {$className} no banco de dados\n";
    $classCode .= "     * \n";
    $classCode .= "     * @return bool\n";
    $classCode .= "     */\n";
    $classCode .= "    public function save(): bool\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        \$this->id = \$db->insert([\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $classCode .= "            '{$name}' => \$this->{$name},\n";
    }
    $classCode .= "        ]);\n";
    $classCode .= "        return true;\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por atualizar o {$className} no banco de dados\n";
    $classCode .= "     * \n";
    $classCode .= "     * @return bool\n";
    $classCode .= "     */\n";
    $classCode .= "    public function update(): bool\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        return \$db->update(\"id = {\$this->id}\", [\n";
    foreach ($fields as $field) {
        list($type, $name) = explode(' ', $field);
        $classCode .= "            '{$name}' => \$this->{$name},\n";
    }
    $classCode .= "        ]);\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por excluir o {$className} no banco de dados\n";
    $classCode .= "     * \n";
    $classCode .= "     * @return bool\n";
    $classCode .= "     */\n";
    $classCode .= "    public function delete(): bool\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        return \$db->delete(\"id = {\$this->id}\");\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por retornar o número total de {$className} no banco de dados\n";
    $classCode .= "     * \n";
    $classCode .= "     * @return int\n";
    $classCode .= "     */\n";
    $classCode .= "    public static function countAll(): int\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        return \$db->select(fields: 'COUNT(*) as count')->fetchObject()->count;\n";
    $classCode .= "    }\n\n";

    $classCode .= "    /**\n";
    $classCode .= "     * Método responsável por retornar todos os {$className} no banco de dados\n";
    $classCode .= "     * \n";
    $classCode .= "     * @return array\n";
    $classCode .= "     */\n";
    $classCode .= "    public static function getUsers(): array\n";
    $classCode .= "    {\n";
    $classCode .= "        \$db = new Database(self::\$tableName);\n";
    $classCode .= "        return \$db->select()->fetchAll(PDO::FETCH_CLASS, self::class);\n";
    $classCode .= "    }\n\n";

    // Fecha a classe
    $classCode .= "}\n";

    // Escreve o código da classe no arquivo de saída
    file_put_contents(ucfirst($outputFile), $classCode);
}

// Processa os argumentos da linha de comando
$options = getopt("", ["name:", "input:", "output:", "tableName:"]);

if (!isset($options['name']) || !isset($options['input']) || !isset($options['output']) || !isset($options['tableName'])) {
    die("Usage: php generate_class.php --name=<className> --input=<inputFile> --output=<outputFile> --tableName=<tableName>\n");
}

$className = $options['name'];
$inputFile = $options['input'];
$outputFile = $options['output'];
$tableName = $options['tableName'];

generateClassFromFields($className, $inputFile, $outputFile, $tableName);

echo "Classe gerada com sucesso em {$outputFile}\n";

<?php

function generateClass($className, $tableName, $fields, $outputFile)
{

    // Inicializa o código da classe
    $classCode = "<?php\n\n";
    $classCode .= "namespace App\Model\Entity;\n\n";
    $classCode .= "use App\Utils\Db_Mngr\Database;\n";
    $classCode .= "use PDO;\n\n";
    $classCode .= "class " . ucfirst($className) . "\n{\n\n";

    // Adiciona o atributo de nome da tabela
    $classCode .= "    /**\n";
    $classCode .= "     * Nome da tabela\n";
    $classCode .= "     * \n";
    $classCode .= "     * @var string\n";
    $classCode .= "     */\n";
    $classCode .= "    private static string \$tableName = '{$tableName}';\n\n";

    // Adiciona os atributos privados
    foreach ($fields as [$type, $name]) {
        $classCode .= "    /**\n";
        $classCode .= "     * @var {$type}\n";
        $classCode .= "     */\n";
        $classCode .= "    private {$type} \${$name};\n\n";
    }

    // Adiciona os getters e setters
    foreach ($fields as [$type, $name]) {
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
    foreach ($fields as [$type, $name]) {
        $constructorParams[] = "{$type} \${$name} = null";
    }
    $classCode .= implode(', ', $constructorParams);
    $classCode .= ")\n";
    $classCode .= "    {\n";
    foreach ($fields as [$type, $name]) {
        $classCode .= "        \$this->set" . ucfirst($name) . "(\${$name});\n";
    }
    $classCode .= "    }\n\n";

    // Adiciona os métodos mágicos __set e __get
    $classCode .= "    public function __set(\$name, \$value)\n";
    $classCode .= "    {\n";
    $classCode .= "        switch (\$name) {\n";
    foreach ($fields as [$type, $name]) {
        $capitalizedField = ucfirst($name);
        $classCode .= "            case '{$name}':\n";
        $classCode .= "                return \$this->set{$capitalizedField}(\$value);\n";
    }
    $classCode .= "        }\n";
    $classCode .= "    }\n\n";

    $classCode .= "    public function __get(\$name)\n";
    $classCode .= "    {\n";
    $classCode .= "        switch (\$name) {\n";
    foreach ($fields as [$type, $name]) {
        $capitalizedField = ucfirst($name);
        $classCode .= "            case '{$name}':\n";
        $classCode .= "                return \$this->get{$capitalizedField}();\n";
    }
    $classCode .= "        }\n";
    $classCode .= "    }\n\n";

    // Adiciona o método setAttrs
    $classCode .= "    public function setAttrs(";
    $setAttrsParams = [];
    foreach ($fields as [$type, $name]) {
        $setAttrsParams[] = "{$type} \${$name} = null";
    }
    $classCode .= implode(', ', $setAttrsParams);
    $classCode .= ")\n";
    $classCode .= "    {\n";
    foreach ($fields as [$type, $name]) {
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
    foreach ($fields as [$type, $name]) {
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
    foreach ($fields as [$type, $name]) {
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

function generateClassFromSchema()
{

    $schemaFile = 'app/model/schema.model';
    $outputDir = 'app/model/entity';

    // Ler o arquivo de schema
    $schemaContent = file_get_contents($schemaFile);
    if (!$schemaContent) {
        die("Erro ao ler o arquivo de schema.\n");
    }

    // Dividir o conteúdo do schema em blocos de classes
    preg_match_all('/(\w+)\s*\{\s*([^}]*)\s*\}/', $schemaContent, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $className = $match[1];
        $classContent = $match[2];

        // Extrair a definição da tabela e os campos
        preg_match('/table\s+(\w+)/', $classContent, $tableMatch);
        $tableName = $tableMatch[1];
        preg_match_all('/(int|string|float)\s+(\w+)/', $classContent, $fieldsMatch, PREG_SET_ORDER);

        $fields = [];
        foreach ($fieldsMatch as $fieldMatch) {
            $fields[] = [$fieldMatch[1], $fieldMatch[2]];
        }

        // Gerar a classe
        generateClass($className, $tableName, $fields, "$outputDir/" . ucfirst($className) . ".php");
    }

    echo "Todas as entidades foram geradas com sucesso em 'App/model/entity' apartir de schema.model\n";
}

//Rodar a funcao
generateClassFromSchema();
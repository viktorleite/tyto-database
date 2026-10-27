# Tyto Database

O Tyto Database é um componente de persistência de dados em PHP 8.5+, projetado para oferecer altíssima performance, segurança rigorosa contra SQL Injection através de Prepared Statements nativos do PDO, e uma API fluida e expressiva inspirada em abordagens fortemente tipadas.

# Sumário

# Sobre o Projeto

# Pré-requisitos

> Lista de requisitos necessários para rodar a biblioteca e como prepará-los:

- **PHP**: Versão 8.5 ou superior.
- **Composer**
- **Extensões PHP obrigatórias**: pdo, pdo_mysql ou pdo_sqlite / pdo_pgsql

```bash
composer --version
```

# Instalação

> Adicione o pacote ao seu projeto via Composer:

```bash
composer require tyto/database
```
# Uso

> 

### 1. Configuração inicial (DatabaseManager)

O DatabaseManager centraliza as configurações da conexão e gerencia a instância singleton do PDO para o ecossistema.

```php
<?php

declare(strict_types=1);

use Tyto\Database\DatabaseManager;

// Configuração para MySQL
DatabaseManager::configure([
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'tyto_app',
    'username' => 'root',
    'password' => 'secret',
    'charset'  => 'utf8mb4'
]);

// Ou configuração para SQLite (ex: em memoria)
DatabaseManager::configure([
    'driver'   => 'sqlite',
    'database' => ':memory:'
]);
```

# Documentação

### API Fluida de Statements (Database & PreparedStatement)
Inspirada em padrões expressivos, esta API permite encadear a tipagem exata dos parâmetros posicionais (?) com total segurança contra injeção SQL[cite: 2].

#### Métodos Disponíveis no PreparedStatement:

- $stmt->setString(int $position, string $value)
- $stmt->setInt(int $position, int $value)
- $stmt->setFloat(int $position, float $value)
- $stmt->setDate(int $position, string $value)
- $stmt->execute(): bool
- $stmt->fetch(): ?array
- $stmt->fetchAll(): array

#### Exemplo de Utilização:
```PHP
<?php

declare(strict_types=1);

use TytoPhp\Database\Database;

// 1. Prepara a instrução SQL
$stmt = Database::prepareStatement(
    "INSERT INTO produtos (nome, quantidade, preco, publicado_em) VALUES (?, ?, ?, ?)"
);

// 2. Atribui os valores fortemente tipados
$stmt->setString(1, "Smartphone Ultra")
     ->setInt(2, 50)
     ->setFloat(3, 2999.99)
     ->setDate(4, date('Y-m-d H:i:s'));

// 3. Executa a query de forma segura
$stmt->execute();

// Para consultas diretas sem parâmetros (ex: comandos SHOW ou utilitários):
$tables = Database::query('SHOW TABLES')->fetchAll();
```

### Construtor de Consultas (QueryBuilder & toSQL())

Permite construir consultas de forma programática, encadeando restrições e inspecionando o comando SQL resultante através do método toSQL().   
```PHP
<?php

declare(strict_types=1);

use TytoPhp\Database\QueryBuilder;

$query = (new QueryBuilder('users'))
    ->where('status', '=', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10);

// Inspeciona a string SQL gerada
echo $query->toSQL(); 
// Saída: SELECT * FROM users WHERE status = ? ORDER BY created_at DESC LIMIT 10

// Executa e retorna os resultados mapeados
$users = $query->get();

// Retorna apenas o primeiro registro correspondente
$singleUser = $query->first();

```

### Mapeamento de Modelos (ActiveRecord)
Facilita a representação de tabelas como entidades orientadas a objetos, integrando-se nativamente com o QueryBuilder.

```PHP
<?php

declare(strict_types=1);

namespace App\Models;

use TytoPhp\Database\ActiveRecord;

final class User extends ActiveRecord
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
}

// Uso prático:
// Buscar por ID de forma otimizada
$user = User::find(1);

// Utilizar escopos do QueryBuilder estaticamente
$activeAdmins = User::query()
    ->where('role', '=', 'admin')
    ->get();
```
### Sistema de Migrations (Migrações)
O Tyto Database conta com um mecanismo próprio de migrações controladas por lotes (batches), garantindo o versionamento consistente do esquema do banco de dados.Exemplo de Arquivo de Migração (database/migrations/2026_03_30_000000_create_users_table.php):
```PHP
<?php

declare(strict_types=1);

use TytoPhp\Database\Migration\Migration;

return new class extends Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(): void
    {
        $this->exec("DROP TABLE IF EXISTS users;");
    }
};
```
#### Executando as Migrações via Script CLI:

```PHP
<?php

use TytoPhp\Database\DatabaseManager;
use TytoPhp\Database\Migration\MigrationRepository;
use TytoPhp\Database\Migration\MigrationRunner;

// Configura o ambiente
DatabaseManager::configure([...]);

// Instancia o Runner e executa o diretório de migrações
$repository = new MigrationRepository();
$runner = new MigrationRunner($repository);

$runner->run(__DIR__ . '/database/migrations');
```

#### Segurança e Boas Práticas
 * Emulação Desativada: O driver PDO é configurado por padrão com ATTR_EMULATE_PREPARES => false, forçando o banco de dados a lidar diretamente com o protocolo de Prepared Statements reais.
 * Validação de Tipos: Sempre utilize os métodos explícitos (setInt, setFloat, setString, setDate) para garantir que os dados passem pelas diretrizes de coerção corretas antes de atingirem o driver de banco de dados.

# Roadmap

# Contribuindo

Se você tiver uma sugestão para melhorar o projeto, faça um fork do repositório e crie uma pull request. Você também pode simplesmente abrir uma **issue** com a tag "**melhoria**". Não se esqueça de dar uma estrela ao projeto! Quaisquer contribuições que você fizer são **muito apreciadas.**

1. Fork do projeto
2. Crie sua branch de recurso (`git checkout -b feature/AmazingFeature`)
3. Confirme suas alterações (`git commit -m 'Add some AmazingFeature'`)
4. Empurre para a Filial (`git push origin feature/AmazingFeature`)
5. Abra uma **pull request**

# Contato

Viktor Leite 
- [@ViktorLeite](https://github.com/viktorleite) 
- contato@viktorleite.com

Website 
- [viktorleite.com.br](https://viktorleite.com.br)

# Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.
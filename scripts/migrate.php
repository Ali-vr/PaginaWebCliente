<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);

require $projectRoot . '/vendor/autoload.php';
Dotenv::createImmutable($projectRoot)->safeLoad();

if (PHP_SAPI !== 'cli') {
   fwrite(STDERR, "Este script solo puede ejecutarse desde la línea de comandos.\n");
   exit(1);
}

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_NAME'] ?? 'Muebleria';
$user = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
$migrationDirectory = $projectRoot . '/src/database/migrations';

try {
   $rootPdo = new PDO(
       sprintf('mysql:host=%s;port=%s;charset=%s', $host, $port, $charset),
       $user,
       $password,
       [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false,
       ],
   );

   $rootPdo->exec(
       sprintf(
           'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
           $dbName,
       ),
   );

   $pdo = new PDO(
       sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset),
       $user,
       $password,
       [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false,
       ],
   );

   $pdo->exec(
       'CREATE TABLE IF NOT EXISTS migraciones (
           id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
           archivo VARCHAR(255) NOT NULL UNIQUE,
           ejecutada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
   );

   $files = glob($migrationDirectory . '/*.sql');
   if ($files === false || $files === []) {
       fwrite(STDOUT, "No se encontraron migraciones en {$migrationDirectory}.\n");
       exit(0);
   }

   usort($files, 'strnatcasecmp');

   $applied = $pdo->query('SELECT archivo FROM migraciones')->fetchAll(PDO::FETCH_COLUMN);
   $appliedSet = array_fill_keys(array_map('strval', $applied), true);

   foreach ($files as $filePath) {
       $filename = basename($filePath);

       if (isset($appliedSet[$filename])) {
           fwrite(STDOUT, "✓ {$filename} — ya aplicada\n");
           continue;
       }

       $sql = file_get_contents($filePath);
       if ($sql === false) {
           throw new RuntimeException("No se pudo leer la migración {$filename}.");
       }

       try {
           $pdo->beginTransaction();
           $pdo->exec($sql);
           $stmt = $pdo->prepare('INSERT INTO migraciones (archivo, ejecutada_en) VALUES (:archivo, NOW())');
           $stmt->execute(['archivo' => $filename]);
           $pdo->commit();
           fwrite(STDOUT, "✓ {$filename} — ejecutada\n");
       } catch (Throwable $exception) {
           if ($pdo->inTransaction()) {
               $pdo->rollBack();
           }
           throw new RuntimeException("Error al ejecutar {$filename}: {$exception->getMessage()}", 0, $exception);
       }
   }

   fwrite(STDOUT, "Migraciones completadas.\n");
   exit(0);
} catch (Throwable $throwable) {
   fwrite(STDERR, $throwable->getMessage() . "\n");
   exit(1);
}

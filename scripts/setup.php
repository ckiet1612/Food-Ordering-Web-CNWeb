<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/bootstrap.php';
$cfg = config('database');
if (!preg_match('/^[a-zA-Z0-9_]+$/', $cfg['database'])) {
    throw new RuntimeException('Ten database chi duoc chua chu, so va dau gach duoi.');
}
$db = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};charset=utf8mb4", $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$name = $cfg['database'];
$db->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$db->exec("USE `{$name}`");
if ($db->query('SHOW TABLES')->fetchAll() !== []) {
    fwrite(STDERR, "Tu choi: database khong trong. Dung scripts/migrate.php de nang cap.\n");
    exit(1);
}
$schema = file_get_contents(BASE_PATH . '/database/schema.sql');
$schema = preg_replace('/^CREATE DATABASE.*;\R|^USE yumio;\R/m', '', $schema);
$db->exec($schema);
if (in_array('--seed', $argv, true)) {
    $seed = preg_replace('/^USE yumio;\R/m', '', file_get_contents(BASE_PATH . '/database/seed.sql'));
    $db->beginTransaction();
    try {
        $db->exec($seed);
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}
echo "Da tao schema moi: {$name}.\n";

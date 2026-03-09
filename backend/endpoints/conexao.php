<?php
$hostname = 'localhost';
$dbname   = 'rpa_bd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro fatal: Não foi possível conectar ao banco de dados. " . $e->getMessage());
}
?>
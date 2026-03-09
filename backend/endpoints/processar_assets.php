<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit;
}

require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE conteudo_pagina SET titulo = :titulo, texto_paragrafo1 = :texto, url_imagem = :url_imagem WHERE id = 1";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':titulo', $_POST['titulo']);
        $stmt->bindParam(':texto', $_POST['texto_paragrafo1']);
        $stmt->bindParam(':url_imagem', $_POST['url_imagem']);
        
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Conteúdo atualizado com sucesso!']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>
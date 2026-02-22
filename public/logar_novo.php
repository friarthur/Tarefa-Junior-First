<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);

if ($email) {
    $stmt = $pdo->prepare("SELECT id FROM lojas WHERE email = ?");
    $stmt->execute([$email]);
    $novaLoja = $stmt->fetch();

    if ($novaLoja) {
        $_SESSION['loja_id'] = $novaLoja['id'];
        header("Location: ../portal/index.php");
        exit;
    }
}
header("Location: index.php?erro=acesso_negado");
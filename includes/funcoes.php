<?php
function gerarRefCodeUnico() {
    return substr(bin2hex(random_bytes(4)), 0, 8);
}

function validarAntifraude($emailIndicado, $emailLoja, $idLoja, $pdo) {
    $emailIndicado = strtolower(trim($emailIndicado));
    $emailLoja = strtolower(trim($emailLoja));

    if ($emailIndicado === $emailLoja) {
        return "Autoindicação não permitida com o e-mail da loja.";
    }

    $stmt = $pdo->prepare("SELECT id FROM indicados WHERE id_loja = ? AND email = ?");
    $stmt->execute([$idLoja, $emailIndicado]);
    
    if ($stmt->fetch()) {
        return "Este e-mail já foi convidado por você anteriormente.";
    }

    return true;
}

function registrarClique($idLoja, $pdo) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    
    $stmt = $pdo->prepare("INSERT INTO cliques (id_loja, ip, user_agent) VALUES (?, ?, ?)");
    return $stmt->execute([$idLoja, $ip, $ua]);
}

function formatarMoeda($valor) {
    return "R$ " . number_format($valor, 2, ',', '.');
}
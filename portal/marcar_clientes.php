<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

$id_indicado = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id_indicado) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT * FROM indicados WHERE id = ? AND status = 'convidado'");
        $stmt->execute([$id_indicado]);
        $indicado = $stmt->fetch();

        if ($indicado) {
            $stmtUp = $pdo->prepare("UPDATE indicados SET status = 'virou_cliente' WHERE id = ?");
            $stmtUp->execute([$id_indicado]);

            $novoTokenPublico = gerarRefCodeUnico();
            
            $stmtNovaLoja = $pdo->prepare("INSERT INTO lojas (nome, email, ref_code) VALUES (?, ?, ?)");
            $stmtNovaLoja->execute([
                $indicado['nome'],
                $indicado['email'],
                $novoTokenPublico
            ]);

            $pdo->commit();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header("Location: index.php?erro=" . urlencode("Falha na conversão: " . $e->getMessage()));
        exit;
    }
}

header("Location: index.php?sucesso_conversao=1");
exit;
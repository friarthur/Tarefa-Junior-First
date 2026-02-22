<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?erro=Metodo invalido');
    exit;
}

$stmtLoja = $pdo->query("SELECT * FROM lojas LIMIT 1");
$lojaLogada = $stmtLoja->fetch();

$nomeIndicado = trim($_POST['nome'] ?? '');
$emailIndicado = trim($_POST['email'] ?? '');

if (empty($nomeIndicado) || !filter_var($emailIndicado, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?erro=Dados invalidos');
    exit;
}

if (strtolower($emailIndicado) === strtolower($lojaLogada['email'])) {
    header('Location: index.php?erro=Voce nao pode indicar sua propria loja');
    exit;
}

$stmtCheck = $pdo->prepare("SELECT id FROM indicados WHERE id_loja = ? AND email = ?");
$stmtCheck->execute([$lojaLogada['id'], $emailIndicado]);
if ($stmtCheck->fetch()) {
    header('Location: index.php?erro=Este e-mail ja foi indicado por voce');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO indicados (id_loja, nome, email, status) VALUES (?, ?, ?, 'convidado')");
    $stmt->execute([$lojaLogada['id'], $nomeIndicado, $emailIndicado]);
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro no banco: ' . urlencode($e->getMessage()));
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'agendwork3@gmail.com'; 
    $mail->Password = 'qeudnjgwikmueasm'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('agendwork3@gmail.com', 'MisterCheff');
    $mail->addAddress($emailIndicado, $nomeIndicado);

    $linkPublico = "http://" . $_SERVER['HTTP_HOST'] . "/public/index.php?ref=" . $lojaLogada['ref_code'];

    $mail->isHTML(true);
    $mail->Subject = 'Convite Especial: ' . $lojaLogada['nome'] . ' te indicou!';
    $mail->Body = "
<div style='background-color:#f3f4f6;padding:40px 0;font-family:Poppins,Arial,sans-serif;color:#1f2937;'>
    <div style='max-width:520px;margin:0 auto;background-color:#ffffff;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,.1);overflow:hidden;'>
        <div style='background-color:#4f46e5;padding:30px;text-align:center;'>
            <h1 style='color:#ffffff;margin:0;font-size:24px;'>MisterCheff</h1>
        </div>
        <div style='padding:40px 35px;text-align:center;'>
            <h2 style='color:#111827;margin:0 0 20px;font-size:22px;'>Você foi indicado!</h2>
            <p style='color:#4b5563;font-size:16px;line-height:1.6;margin:0 0 30px;'>
                A loja <strong>{$lojaLogada['nome']}</strong> acredita no seu potencial e te indicou para se tornar uma unidade parceira do MisterCheff.
            </p>
            <a href='{$linkPublico}' style='display:inline-block;background:#4f46e5;color:#ffffff;padding:16px 32px;border-radius:12px;font-size:16px;font-weight:600;text-decoration:none;box-shadow:0 4px 6px rgba(79,70,229,.2);'>
                Aceitar Convite e Ser uma Loja
            </a>
            <p style='color:#9ca3af;font-size:14px;margin:30px 0 0;'>
                Ao clicar, você será redirecionado para conhecer os benefícios.
            </p>
        </div>
        <div style='background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #f3f4f6;'>
            <p style='font-size:12px;color:#6b7280;margin:0;'>
                Sistema Indique e Ganhe · MisterCheff
            </p>
        </div>
    </div>
</div>";

    $mail->send();
    header("Location: index.php?sucesso=1");
    exit;

} catch (Exception $e) {
    header("Location: index.php?erro=Erro ao enviar e-mail: " . urlencode($mail->ErrorInfo));
    exit;
}
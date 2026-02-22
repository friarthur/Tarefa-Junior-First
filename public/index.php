<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

$ref = filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$ref) {
    die("Link de indicação inválido ou expirado.");
}

$stmt = $pdo->prepare("SELECT id, nome FROM lojas WHERE ref_code = ?");
$stmt->execute([$ref]);
$loja = $stmt->fetch();

if (!$loja) {
    die("Indicação não localizada no sistema.");
}

registrarClique($loja['id'], $pdo);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MisterCheff · Convite Especial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; color: #1e293b; }
        .card { background: white; padding: 3.5rem 2.5rem; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.06); text-align: center; max-width: 480px; width: 90%; }
        .icon-container { background: #eef2ff; color: #4f46e5; width: 80px; height: 80px; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; }
        h1 { font-size: 1.75rem; font-weight: 600; margin-bottom: 1rem; color: #0f172a; }
        p { color: #64748b; font-size: 1.05rem; line-height: 1.6; margin-bottom: 2.5rem; }
        .store-name { color: #4f46e5; font-weight: 600; }
        .btn { background: #4f46e5; color: white; padding: 1rem 2rem; border-radius: 14px; text-decoration: none; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; border: none; }
        .btn:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon-container">
            <i data-lucide="party-popper" style="width: 40px; height: 40px;"></i>
        </div>
        
        <h1>Você foi convidado!</h1>
        
        <p>A unidade <span class="store-name"><?php echo htmlspecialchars($loja['nome']); ?></span> indicou você para fazer parte do <strong>MisterCheff</strong>.</p>
        
        <a href="/portal/index.php" class="btn">
            <span>Acessar o Portal</span>
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        </a>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
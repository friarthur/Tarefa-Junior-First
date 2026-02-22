<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

// Busca a loja para a sessão (Ajuste conforme seu banco)
$stmt = $pdo->query("SELECT * FROM lojas LIMIT 1");
$loja = $stmt->fetch();

if (!$loja) {
    die("Erro: Nenhuma loja encontrada no banco.");
}


$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . "/public/index.php?ref=";
$linkIndicacao = $baseUrl . $loja['ref_code'];

$stmtBonus = $pdo->prepare("SELECT COUNT(*) as total FROM indicados WHERE id_loja = ? AND status = 'virou_cliente'");
$stmtBonus->execute([$loja['id']]);
$conversoes = $stmtBonus->fetch()['total'];
$totalDesconto = $conversoes * 100;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Portal da Loja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="/portal/css/portal.css">
</head>
<body>

<aside class="sidebar">
    <div class="logo-area"><span class="logo-span">Mister</span>Cheff</div>
    <div class="nav-links">
        <div class="nav-item active"><i data-lucide="home"></i><span>Início</span></div>
        <div class="nav-item"><i data-lucide="megaphone"></i><span>Indique e Ganhe</span></div>
        <div class="nav-item"><i data-lucide="settings"></i><span>Configurações</span></div>
    </div>
    <div style="color:#cbd5e1; font-size:0.75rem; padding: 0 8px;">v2.15 · <?php echo htmlspecialchars($loja['nome']); ?></div>
</aside>

<main class="main">
    <header class="header">
        <div class="store-selector">
            <button class="dropdown-btn">
                <i data-lucide="store"></i>
                <span><?php echo htmlspecialchars($loja['nome']); ?></span>
            </button>
        </div>
        <button class="profile-btn">
            <i data-lucide="circle-user-round"></i>
            <span><?php echo explode(' ', $loja['nome'])[0]; ?></span>
        </button>
    </header>

    <section class="cards-grid">
        <div class="stat-card card-verde">
            <div class="card-title">Bônus Acumulado</div>
            <div class="card-value">R$ <?php echo number_format($totalDesconto, 2, ',', '.'); ?></div>
            <div class="card-sub">Total de <span><?php echo $conversoes; ?></span> conversões</div>
        </div>
        <div class="stat-card card-azul">
            <div class="card-title">Indicações Ativas</div>
            <div class="card-value">
                <?php 
                    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM indicados WHERE id_loja = ?");
                    $stmtCount->execute([$loja['id']]);
                    echo $stmtCount->fetchColumn();
                ?>
            </div>
            <div class="card-sub">convites enviados</div>
        </div>
    </section>

    <section class="charts-row">
        <div class="chart-card">
            <div class="chart-title"><i data-lucide="share-2"></i> Seu Link de Indicação</div>
            <div style="margin-top: 1rem;">
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">Copie o link abaixo e compartilhe com outros estabelecimentos.</p>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="linkRef" value="<?php echo $linkIndicacao; ?>" readonly 
                           style="flex: 1; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.8rem;">
                    <button onclick="copiarLink()" class="profile-btn" style="background: #4f46e5; color: white;">Copiar</button>
                </div>
                <button onclick="compartilharZap()" class="profile-btn" style="width: 100%; margin-top: 10px; justify-content: center; background: #25d366; color: white; border: none;">
                    <i data-lucide="message-circle"></i> WhatsApp
                </button>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title"><i data-lucide="plus-circle"></i> Novo Indicado</div>
            <form action="cadastrar.php" method="POST" style="margin-top: 1rem; display: flex; flex-direction: column; gap: 8px;">
                <input type="text" name="nome" placeholder="Nome da Loja" required style="padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
                <input type="email" name="email" placeholder="E-mail para convite" required style="padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
                <button type="submit" style="padding: 10px; border-radius: 10px; background: #1e293b; color: white; border: none; cursor: pointer; font-weight: 600;">Enviar Convite</button>
            </form>
            <?php if(isset($_GET['sucesso'])): ?> <small style="color: green;">Convite registrado!</small> <?php endif; ?>
            <?php if(isset($_GET['erro'])): ?> <small style="color: red;"><?php echo htmlspecialchars($_GET['erro']); ?></small> <?php endif; ?>
        </div>
    </section>

    <section class="chart-card">
        <div class="chart-title"><i data-lucide="users"></i> Status das Indicações</div>
        <div style="overflow-x: auto; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 0.8rem;">
                        <th style="padding: 10px;">LOJA INDICADA</th>
                        <th style="padding: 10px;">STATUS</th>
                        <th style="padding: 10px;">DATA</th>
                        <th style="padding: 10px;">AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmtList = $pdo->prepare("SELECT * FROM indicados WHERE id_loja = ? ORDER BY data_criacao DESC");
                    $stmtList->execute([$loja['id']]);
                    while($ind = $stmtList->fetch()):
                    ?>
                    <tr style="border-bottom: 1px solid #f8fafc; font-size: 0.85rem;">
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($ind['nome']); ?></td>
                        <td style="padding: 12px;">
                            <span style="padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; background: <?php echo $ind['status'] == 'virou_cliente' ? '#dcfce7; color: #166534' : '#fef9e7; color: #854d0e'; ?>;">
                                <?php echo strtoupper($ind['status']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo date('d/m/y', strtotime($ind['data_criacao'])); ?></td>
                        <td style="padding: 12px;">
                            <?php if($ind['status'] == 'convidado'): ?>
                                <a href="/portal/marcar_clientes.php?id=<?php echo $ind['id']; ?>" style="color: #4f46e5; text-decoration: none; font-weight: 600;">Virou Cliente?</a>
                            <?php else: ?>
                                <i data-lucide="check-circle-2" style="color: #10b981; width: 18px;"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="text-muted" style="margin-top: 2rem;">Sistema de Fidelidade MisterCheff · Logado como <?php echo htmlspecialchars($loja['email']); ?></div>
</main>

<script>
    lucide.createIcons();

    function copiarLink() {
        const link = document.getElementById('linkRef');
        link.select();
        navigator.clipboard.writeText(link.value);
        alert('Link copiado!');
    }

    function compartilharZap() {
        const link = document.getElementById('linkRef').value;
        const texto = encodeURIComponent('Ei, vira parceiro do MisterCheff usando meu link: ' + link);
        window.open('https://api.whatsapp.com/send?text=' + texto);
    }
</script>
</body>
</html>
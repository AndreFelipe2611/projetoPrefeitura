<?php
include("../conexao.php");

// Buscar colunas da tabela registros (exceto 'id' e 'caminhoArquivo')
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
while ($coluna = $colunas_resultado->fetch_assoc()) {
    if ($coluna['Field'] !== 'id' && $coluna['Field'] !== 'caminhoArquivo') {
        $colunas_tabela[] = $coluna['Field'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Registros - Prefeitura</title>
    <link rel="stylesheet" href="../assets/dashboard2.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Painel de Registros da Prefeitura</h1>
        <p>Visualização dos dados enviados pela Ansal.</p>
    </header>

    <div class="caixa-tabela">
        <table class="tabela-registros">
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($colunas_tabela as $coluna): ?>
                        <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                    <?php endforeach; ?>
                    <th>Anexo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while ($linha = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $linha['id']; ?></td>
                            <?php foreach ($colunas_tabela as $coluna): ?>
                                <td><?php echo htmlspecialchars($linha[$coluna] ?? 'N/A'); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?php if (!empty($linha['caminhoArquivo'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($linha['caminhoArquivo']); ?>" target="_blank" class="acao-visualizar">
                                        Visualizar Anexo
                                    </a>
                                <?php else: ?>
                                    <span>Sem anexo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="<?php echo count($colunas_tabela) + 2; ?>">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

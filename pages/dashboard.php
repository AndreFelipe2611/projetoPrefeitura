<?php
include("../conexao.php");

// Buscar colunas da tabela registros diretamente (exceto 'id' e 'caminho_arquivo')
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];

while ($coluna = $colunas_resultado->fetch_assoc()) {
    if ($coluna['Field'] !== 'id' && $coluna['Field'] !== 'caminho_arquivo') {
        $colunas_tabela[] = $coluna['Field'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Ansal</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>
    <div class="menu-lateral">
        <div class="logo">Ansal</div>
        <button id="botao-menu" class="botao-menu" onclick="alternarAbas()">☰</button>
        <div id="abas" class="abas">
            <button class="botao-verde">Verificados</button>
            <button class="botao-vermelho">Não Verificados</button>
            <button class="botao-historico" onclick="mostrarTabela()">Histórico</button>
            <button class="botao-adicionar" onclick="mostrarFormulario()">Adicionar</button>
            <a href="../logout.php">Sair</a>
        </div>
    </div>

    <div class="conteudo-principal">
        <div id="formulario-adicao" style="display:none; margin-top: 20px;">
            <form method="POST" action="../controles/salvarRegistro.php" enctype="multipart/form-data">
                <?php foreach ($colunas_tabela as $coluna): ?>
                    <div>
                        <label><?php echo htmlspecialchars(ucfirst($coluna)); ?>:</label>
                        <input type="text" name="dados[<?php echo htmlspecialchars($coluna); ?>]" required>
                    </div>
                <?php endforeach; ?>
                <div>
                    <label>Enviar Arquivo/Foto:</label>
                    <input type="file" name="arquivo" style="color: white;">
                </div>
                <button type="submit">Salvar</button>
            </form>
        </div>

        <div class="caixa-tabela" id="tabela-historico">
            <table class="tabela-principal">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ($colunas_tabela as $coluna): ?>
                            <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                        <?php endforeach; ?>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                    while ($linha = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $linha['id']; ?></td>
                            <?php foreach ($colunas_tabela as $coluna): ?>
                                <td><?php echo htmlspecialchars($linha[$coluna] ?? ''); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?php if (!empty($linha['caminho_arquivo'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($linha['caminho_arquivo']); ?>" target="_blank" class="acao visualizar" title="Visualizar anexo">
                                        👁️ Ver
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function alternarAbas() {
            const abas = document.getElementById("abas");
            const botao = document.getElementById("botao-menu");
            abas.classList.toggle("mostrar");
            botao.classList.toggle("ativo");
        }

        function mostrarFormulario() {
            document.getElementById("formulario-adicao").style.display = "block";
            document.getElementById("tabela-historico").style.display = "none";
        }

        function mostrarTabela() {
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "block";
        }

        // Mostrar a tabela por padrão ao carregar a página
        document.addEventListener("DOMContentLoaded", function() {
            mostrarTabela();
        });
    </script>
</body>
</html>
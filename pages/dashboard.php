<?php
include("../conexao.php");

// Buscar colunas da tabela registros, exceto 'id' e 'caminho_arquivo'
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];

while ($coluna = $colunas_resultado->fetch_assoc()) {
    $campo = $coluna['Field'];
    if ($campo !== 'id' && $campo !== 'caminhoArquivo' && $campo !== 'caminho_arquivo') {
        $colunas_tabela[] = $campo;
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
            <button class="botao-historico" onclick="mostrarTabela()">Histórico</button>
            <button class="botao-adicionar" onclick="mostrarFormulario()">Adicionar</button>
            <button onclick="filtrarTabela('verificado')">Verificados</button>
            <button onclick="filtrarTabela('nao-verificado')">Não Verificados</button>
            <a href="../logout.php">Sair</a>
        </div>
    </div>

    <div class="conteudo-principal">
        <div id="mensagem-boas-vindas" class="mensagem-central">
            <h2>Olá caro colaborador, bora trabalhar??</h2>
        </div>

        <!-- FORMULÁRIO DE ADIÇÃO -->
        <div id="formulario-adicao" style="display:none; margin-top: 20px;">
            <form method="POST" action="../controles/salvarRegistro.php" enctype="multipart/form-data">
                <?php foreach ($colunas_tabela as $coluna): ?>
                    <div>
                        <label><?php echo ucfirst($coluna); ?>:</label>
                        <input type="text" name="dados[<?php echo $coluna; ?>]" required>
                    </div>
                <?php endforeach; ?>
                <div>
                    <label>Arquivo / Foto:</label>
                    <input type="file" name="arquivo" style="color: white;">
                </div>
                <button type="submit">Salvar</button>
            </form>
        </div>

        <!-- TABELA HISTÓRICO -->
        <div id="tabela-historico" class="caixa-tabela" style="display:none;">
            <table class="tabela-registros">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ($colunas_tabela as $coluna): ?>
                            <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                        <?php endforeach; ?>
                        <th>Anexo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                    if ($result->num_rows > 0):
                        while ($linha = $result->fetch_assoc()):
                            $status = (isset($linha['concluido']) && $linha['concluido'] == 1) ? '✅ Verificado' : '❌ Não Verificado';
                    ?>
                        <tr>
                            <td><?php echo $linha['id']; ?></td>
                            <?php foreach ($colunas_tabela as $coluna): ?>
                                <td><?php echo htmlspecialchars($linha[$coluna] ?? 'N/A'); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?php if (!empty($linha['caminhoArquivo']) || !empty($linha['caminho_arquivo'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($linha['caminhoArquivo'] ?: $linha['caminho_arquivo']); ?>" target="_blank" class="acao-visualizar">
                                        Visualizar Anexo
                                    </a>
                                <?php else: ?>
                                    <span>Sem anexo</span>
                                <?php endif; ?>
                            </td>
                            <td class="status-coluna"><?php echo $status; ?></td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="<?php echo count($colunas_tabela) + 3; ?>">Nenhum registro encontrado.</td>
                        </tr>
                    <?php endif; ?>
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
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "block";
            document.getElementById("tabela-historico").style.display = "none";
        }

        function mostrarTabela() {
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "block";
            filtrarTabela('todos');
        }

        function filtrarTabela(filtro) {
            const linhas = document.querySelectorAll("#tabela-historico tbody tr");
            linhas.forEach(linha => {
                const textoStatus = linha.querySelector("td.status-coluna")?.textContent || '';
                const status = textoStatus.includes('✅') ? 'verificado' : 'nao-verificado';
                if (filtro === 'todos' || filtro === status) {
                    linha.style.display = "";
                } else {
                    linha.style.display = "none";
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "none";
            document.getElementById("mensagem-boas-vindas").style.display = "block";
        });
    </script>
</body>
</html>

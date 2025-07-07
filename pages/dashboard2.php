<?php
include("../conexao.php");

// Buscar colunas da tabela registros (exceto 'id' e 'caminhoArquivo')
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
while ($coluna = $colunas_resultado->fetch_assoc()) {
    if ($coluna['Field'] !== 'id' && $coluna['Field'] !== 'caminhoArquivo' && $coluna['Field'] !== 'concluido') {
        $colunas_tabela[] = $coluna['Field'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Prefeitura - Dashboard</title>
    <link rel="stylesheet" href="../assets/dashboard2.css" />
</head>
<body>

<div class="menu-lateral">
    <div class="logo">Prefeitura</div>
    <button id="botao-menu" class="botao-menu" onclick="alternarAbas()">☰</button>
    <div id="abas" class="abas">
        <button class="botao-historico" onclick="mostrarTabela('nao')">Não Concluídos</button>
        <button class="botao-historico" onclick="mostrarTabela('sim')">Concluídos</button>
        <button class="botao-historico" onclick="mostrarTabela('todos')">Todos</button>
        <a href="../logout.php">Sair</a>
    </div>
</div>

<div class="conteudo-principal">
    <div id="mensagem-boas-vindas" class="mensagem-central">
        <h2>Olá caro colaborador, bora trabalhar??</h2>
    </div>

    <div id="tabela-historico" class="caixa-tabela" style="display:none;">
        <table class="tabela-registros">
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($colunas_tabela as $coluna): ?>
                        <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                    <?php endforeach; ?>
                    <th>Anexo</th>
                    <th>Concluído</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while ($linha = $result->fetch_assoc()):
                        $concluido = (int)($linha['concluido'] ?? 0);
                    ?>
                    <tr data-concluido="<?php echo $concluido ? 'sim' : 'nao'; ?>" data-id="<?php echo $linha['id']; ?>">
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
                        <td class="coluna-checkbox">
                            <input type="checkbox" class="checkbox-concluido" <?php echo $concluido ? 'checked' : ''; ?> />
                        </td>
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

    function mostrarTabela(filtro) {
        document.getElementById("mensagem-boas-vindas").style.display = "none";
        const linhas = document.querySelectorAll(".tabela-registros tbody tr");

        linhas.forEach(tr => {
            const status = tr.getAttribute("data-concluido");
            if (filtro === 'todos') {
                tr.style.display = "";
            } else {
                tr.style.display = (status === filtro) ? "" : "none";
            }
        });

        document.getElementById("tabela-historico").style.display = "block";
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Inicializa com a mensagem de boas-vindas
        document.getElementById("mensagem-boas-vindas").style.display = "flex";
        document.getElementById("tabela-historico").style.display = "none";

        // Configura evento para cada checkbox
        document.querySelectorAll(".checkbox-concluido").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                const tr = this.closest("tr");
                const id = tr.getAttribute("data-id");
                const novoStatus = this.checked ? 1 : 0;

                // Atualiza atributo visual
                tr.setAttribute("data-concluido", this.checked ? "sim" : "nao");

                // Faz requisição AJAX para salvar no banco
                fetch("../controles/checkout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${encodeURIComponent(id)}&concluido=${encodeURIComponent(novoStatus)}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Resposta do servidor:", data);
                })
                .catch(err => {
                    alert("Erro ao salvar o status!");
                    console.error(err);
                });
            });
        });
    });
</script>

</body>
</html>

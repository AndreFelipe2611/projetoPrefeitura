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
    <link rel="stylesheet" href="../assets/componentes.css">
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
        <?php include("../subPages/adicionar.php"); ?>
        <?php include("../subPages/historico.php"); ?>
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

<?php
include("../conexao.php");

// Buscar colunas da tabela registros (exceto 'id', 'caminhoArquivo' e 'concluido')
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
while ($coluna = $colunas_resultado->fetch_assoc()) {
    if (!in_array($coluna['Field'], ['id', 'anexos', 'concluido', 'caminhoArquivo'])) {
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
        <button class="botao-vistosno" onclick="mostrarTabela('nao')">Não Concluídos</button>
        <button class="botao-vistos" onclick="mostrarTabela('sim')">Concluídos</button>
        <button class="botao-historico" onclick="mostrarTabela('todos')">Todos</button>
        <a href="../logout.php">Sair</a>
    </div>
</div>

<div class="conteudo-principal">
    <div id="mensagem-boas-vindas" class="mensagem-central">
        <h2>Olá caro colaborador</h2>
    </div>

   <?php
    include("../subPages/historico2.php");
   ?>
</div>

 <script src="../assets/dashboard2.js"></script>

</body>
</html>

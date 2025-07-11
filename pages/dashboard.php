<?php
include("../conexao.php");

// Buscar colunas da tabela registros, ignorando campos indesejados
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
$colunas_ignoradas = ['id', 'caminhoArquivo', 'caminho_arquivo', 'concluido'];

while ($coluna = $colunas_resultado->fetch_assoc()) {
    $campo = $coluna['Field'];
    if (!in_array($campo, $colunas_ignoradas)) {
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
            <button onclick="filtrarVerificados()" class="verificados">Verificados</button>
            <button onclick="filtrarNaoVerificados()" class="noverificados">Não Verificados</button>
            <a href="../logout.php">Sair</a>
        </div>
    </div>


    <div class="conteudo-principal">
        <div id="mensagem-boas-vindas" class="mensagem-central">
            <h2>Olá caro colaborador, bora trabalhar??</h2>
        </div>

       <?php
     include("../subPages/adicionar.php");
     include("../subPages/historico.php");
    
    ?>
       
    </div>
    <script src="../assets/dashboard.js"></script>
</body>
</html>

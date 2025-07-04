<?php
include("../conexao.php");

$dados = $_POST['dados'] ?? [];
$caminho_arquivo_final = null;

// --- Lógica para o Upload do Arquivo ---
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
    $diretorio_uploads = '../uploads/';
    
    // Gera um nome de arquivo único para evitar que um arquivo substitua outro
    $nome_arquivo = uniqid() . '_' . basename($_FILES["arquivo"]["name"]);
    $caminho_completo = $diretorio_uploads . $nome_arquivo;

    // Tenta mover o arquivo da pasta temporária do PHP para a nossa pasta 'uploads'
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho_completo)) {
        // Se deu certo, guardamos o nome do arquivo para salvar no banco
        $caminho_arquivo_final = $nome_arquivo;
    } else {
        echo "Erro ao mover o arquivo enviado.";
        exit;
    }
}
// --- Fim da Lógica de Upload ---

// Adiciona o caminho do arquivo (se houver) aos outros dados do formulário
if ($caminho_arquivo_final) {
    $dados['caminho_arquivo'] = $caminho_arquivo_final;
}

// Prepara o SQL para inserir os dados de forma segura
if (!empty($dados)) {
    $colunas = array_keys($dados);
    $valores = array_values($dados);
    
    // Monta a string de colunas (`rua`, `numero`, `caminho_arquivo`)
    $nomes_colunas = implode(", ", array_map(function($col) { return "`$col`"; }, $colunas));
    
    // Monta a string de placeholders para o prepared statement (?, ?, ?)
    $placeholders = implode(", ", array_fill(0, count($valores), '?'));

    // Usando "prepared statements" para evitar injeção de SQL
    $stmt = $conexao->prepare("INSERT INTO registros ($nomes_colunas) VALUES ($placeholders)");

    // Define os tipos dos parâmetros (s = string, i = integer, d = double)
    // Como todos os seus campos são TEXT, usaremos 's' para todos.
    $tipos = str_repeat('s', count($valores));
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        // Redireciona de volta para a página do dashboard após o sucesso
        header("Location: ../pages/dashboard.php?status=sucesso");
    } else {
        echo "Erro ao salvar registro: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Nenhum dado para salvar.";
}

$conexao->close();
?>
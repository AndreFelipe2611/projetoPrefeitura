<?php
include("../conexao.php");

$dados = $_POST['dados'] ?? [];
$caminhoArquivoFinal = null;

// --- Lógica para o Upload do Arquivo ---
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
    $diretorioUploads = '../uploads/';
    
    // Gera um nome de arquivo único
    $nomeArquivo = uniqid() . '_' . basename($_FILES["arquivo"]["name"]);
    $caminhoCompleto = $diretorioUploads . $nomeArquivo;

    // Move o arquivo
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoCompleto)) {
        $caminhoArquivoFinal = $nomeArquivo;
    } else {
        echo "Erro ao mover o arquivo enviado.";
        exit;
    }
}
// --- Fim da Lógica de Upload ---

// Adiciona o caminho do arquivo (se houver) aos dados
if ($caminhoArquivoFinal) {
    $dados['caminhoArquivo'] = $caminhoArquivoFinal;
}

// Prepara o SQL para inserir os dados
if (!empty($dados)) {
    $colunas = array_keys($dados);
    $valores = array_values($dados);
    
    // Monta a string de colunas (`nome`, `caminhoArquivo`)
    $nomesColunas = implode(", ", array_map(function($col) { return "`$col`"; }, $colunas));
    
    // Placeholders (?, ?, ?)
    $placeholders = implode(", ", array_fill(0, count($valores), '?'));

    // Prepared statement
    $stmt = $conexao->prepare("INSERT INTO registros ($nomesColunas) VALUES ($placeholders)");

    // Assume que todos os campos são texto (s)
    $tipos = str_repeat('s', count($valores));
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
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

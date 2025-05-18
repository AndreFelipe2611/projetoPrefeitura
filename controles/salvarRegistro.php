<?php
include("../conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['dados'])) {
    $dados = $_POST['dados'];

    // Preparar os campos e valores
    $campos = array_keys($dados);
    $valores = array_values($dados);

    $colunas = implode(", ", array_map(fn($c) => "`$c`", $campos));
    $placeholders = implode(", ", array_fill(0, count($valores), "?"));

    $stmt = $conexao->prepare("INSERT INTO registros ($colunas) VALUES ($placeholders)");

    // Criar os tipos dinamicamente (todos como string por segurança aqui)
    $tipos = str_repeat("s", count($valores));
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        header("Location: ../pages/dashboard.php"); 
        exit;
    } else {
        echo "Erro ao salvar registro: " . $stmt->error;
    }
} else {
    echo "Requisição inválida.";
}

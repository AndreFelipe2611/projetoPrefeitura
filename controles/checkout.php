<?php
include("../conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $concluido = $_POST['concluido'] ?? null;

    if ($id !== null && ($concluido === "0" || $concluido === "1")) {
        $stmt = $conexao->prepare("UPDATE registros SET concluido = ? WHERE id = ?");
        $stmt->bind_param("ii", $concluido, $id);

        if ($stmt->execute()) {
            echo "Status atualizado com sucesso.";
        } else {
            http_response_code(500);
            echo "Erro ao atualizar status.";
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo "Dados inválidos.";
    }
} else {
    http_response_code(405);
    echo "Método não permitido.";
}

<?php
if (isset($_POST['excluirId'])) {
    $registroId = (int) $_POST['excluirId'];

    $stmt = $conexao->prepare("DELETE FROM registros WHERE id = ?");
    $stmt->bind_param("i", $registroId);
    $stmt->execute();
    $stmt->close();

    $mensagem = "Registro ID $registroId excluído!";
}
?>

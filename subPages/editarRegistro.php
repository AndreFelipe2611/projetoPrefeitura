<?php
include("../conexao.php");

if (!isset($_GET['id'])) {
    die("ID não fornecido.");
}

$id = (int) $_GET['id'];

$registro = $conexao->query("SELECT * FROM registros WHERE id = $id")->fetch_assoc();

if (!$registro) {
    die("Registro não encontrado.");
}

$colunas = [];
$res = $conexao->query("SHOW COLUMNS FROM registros");
while ($row = $res->fetch_assoc()) {
    if ($row['Field'] !== 'id') {
        $colunas[] = $row['Field'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [];
    foreach ($colunas as $coluna) {
        $valor = $conexao->real_escape_string($_POST[$coluna] ?? '');
        $valores[] = "$coluna = '$valor'";
    }

    $sql = "UPDATE registros SET " . implode(", ", $valores) . " WHERE id = $id";
    if ($conexao->query($sql)) {
        header("Location: admin.php");
        exit;
    } else {
        echo "Erro ao atualizar: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
</head>
<body>
    <h1>Editar Registro #<?= $id ?></h1>
    <form method="POST">
        <?php foreach ($colunas as $col): ?>
            <label><?= htmlspecialchars($col) ?>:</label>
            <input type="text" name="<?= $col ?>" value="<?= htmlspecialchars($registro[$col]) ?>"><br><br>
        <?php endforeach; ?>
        <button type="submit">Salvar</button>
        <a href="admin.php">Cancelar</a>
    </form>
</body>
</html>

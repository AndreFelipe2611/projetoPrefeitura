<?php
include("../conexao.php");

$mensagem = "";

// Adicionar usuário
if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = trim($_POST['usuario']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, senha) VALUES ('$usuario', '$senha')";
    if ($conexao->query($sql)) {
        $mensagem = "Usuário adicionado com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar usuário: " . $conexao->error;
    }
}

// Adicionar nome de coluna na tabela e criar a coluna no banco
if (isset($_POST['coluna_tabela'])) {
    $coluna_tabela = trim($_POST['coluna_tabela']);
    if ($coluna_tabela !== "") {
        // Primeiro, inserir no controle
        $sql = "INSERT INTO colunas_tabela (nome) VALUES ('$coluna_tabela')";
        if ($conexao->query($sql)) {
            // Verifica se a coluna já existe na tabela 'registros'
            $verifica_sql = "SHOW COLUMNS FROM registros LIKE '$coluna_tabela'";
            $verifica_resultado = $conexao->query($verifica_sql);
            if ($verifica_resultado->num_rows === 0) {
                // Depois, criar a coluna real
                $alter_sql = "ALTER TABLE registros ADD `$coluna_tabela` TEXT";
                if ($conexao->query($alter_sql)) {
                    $mensagem = "Coluna '$coluna_tabela' adicionada à tabela!";
                } else {
                    $mensagem = "Erro ao criar coluna no banco: " . $conexao->error;
                }
            } else {
                $mensagem = "A coluna '$coluna_tabela' já existe!";
            }
        } else {
            $mensagem = "Erro ao adicionar coluna: " . $conexao->error;
        }
    }
}

// Buscar colunas e registros para pré-visualização
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
while ($coluna = $colunas_resultado->fetch_assoc()) {
    if ($coluna['Field'] !== 'id') {
        $colunas_tabela[] = $coluna['Field'];
    }
}
$registros_resultado = $conexao->query("SELECT * FROM registros ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="./admin.css">
</head>
<body>

    <h1 class="titulo">Painel do Administrador</h1>

    <?php if ($mensagem): ?>
        <p class="mensagem"><?= $mensagem ?></p>
    <?php endif; ?>

    <div class="secao-admin">
        <h2>Adicionar Usuário</h2>
        <form method="POST" class="linha-usuario">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Adicionar</button>
        </form>
    </div>

    <div class="linha-preta"></div>

    <div class="secao-admin">
        <h2>Adicionar coluna à Tabela</h2>
        <form method="POST">
            <input type="text" name="coluna_tabela" placeholder="Nome da coluna da tabela" required>
            <button type="submit">Adicionar</button>
        </form>
        <ul>
            <?php foreach ($colunas_tabela as $col): ?>
                <li><?= htmlspecialchars($col) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="linha-preta"></div>

    <div class="secao-admin">
        <h2>Pré-visualização da Tabela de Registros</h2>
        <div style="overflow-x:auto;">
            <table border="1" cellpadding="8" cellspacing="0">
                <thead style="background-color: #4285f4; color: white;">
                    <tr>
                        <th>ID</th>
                        <?php foreach ($colunas_tabela as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $registros_resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $linha['id'] ?></td>
                            <?php foreach ($colunas_tabela as $col): ?>
                                <td><?= htmlspecialchars($linha[$col] ?? '') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

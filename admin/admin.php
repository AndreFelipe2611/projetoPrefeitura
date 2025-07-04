<?php
include("../conexao.php");

$mensagem = "";

// Adicionar usuário
if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = trim($_POST['usuario']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $senha);
    if ($stmt->execute()) {
        $mensagem = "Usuário adicionado com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar usuário: " . $stmt->error;
    }
    $stmt->close();
}

// Adicionar nome de coluna na tabela e criar a coluna no banco
if (isset($_POST['coluna_tabela'])) {
    $coluna_tabela = trim($_POST['coluna_tabela']);
    $coluna_tabela = preg_replace('/[^a-zA-Z0-9_]/', '', $coluna_tabela);

    if ($coluna_tabela !== "") {
        $coluna_escaped = $conexao->real_escape_string($coluna_tabela);

        // Verifica se a coluna já existe
        $verifica_sql = "SHOW COLUMNS FROM registros LIKE '$coluna_escaped'";
        $verifica_resultado = $conexao->query($verifica_sql);

        if ($verifica_resultado->num_rows === 0) {
            // Cria a nova coluna
            $alter_sql = "ALTER TABLE registros ADD `$coluna_escaped` TEXT";
            if ($conexao->query($alter_sql)) {
                $mensagem = "Coluna '$coluna_tabela' adicionada à tabela!";
            } else {
                $mensagem = "Erro ao criar coluna no banco: " . $conexao->error;
            }
        } else {
            $mensagem = "Erro: A coluna '$coluna_tabela' já existe!";
        }
    }
}

// Buscar colunas para pré-visualização
$colunas_resultado = $conexao->query("SHOW COLUMNS FROM registros");
$colunas_tabela = [];
while ($coluna = $colunas_resultado->fetch_assoc()) {
    if ($coluna['Field'] !== 'id' && $coluna['Field'] !== 'caminho_arquivo') {
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

    <?php if ($mensagem): 
        $classe_mensagem = (stripos($mensagem, 'erro') !== false) ? 'erro' : 'sucesso';
    ?>
        <p class="mensagem <?= $classe_mensagem ?>"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <div class="secao-admin">
        <h2>Adicionar Usuário</h2>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Nome do usuário (ex: ansal)" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Adicionar Usuário</button>
        </form>
    </div>

    <div class="linha-preta"></div>

    <div class="secao-admin">
        <h2>Adicionar Coluna à Tabela de Registros</h2>
        <form method="POST">
            <input type="text" name="coluna_tabela" placeholder="Nome da coluna (ex: observacao)" required>
            <button type="submit">Adicionar Coluna</button>
        </form>
        <h3>Colunas Atuais:</h3>
        <ul>
            <?php foreach ($colunas_tabela as $col): ?>
                <li><?= htmlspecialchars($col) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="linha-preta"></div>

    <div class="secao-admin">
        <h2>Pré-visualização dos Últimos 5 Registros</h2>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <?php 
                        $colunas_preview_res = $conexao->query("SHOW COLUMNS FROM registros");
                        $colunas_preview = [];
                        while($c = $colunas_preview_res->fetch_assoc()){
                            if($c['Field'] !== 'id'){
                                $colunas_preview[] = $c['Field'];
                            }
                        }

                        foreach ($colunas_preview as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $registros_resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $linha['id'] ?></td>
                            <?php foreach ($colunas_preview as $col): ?>
                                <td>
                                    <?php
                                    $texto = htmlspecialchars($linha[$col] ?? '');
                                    echo strlen($texto) > 40 ? substr($texto, 0, 40) . '...' : $texto;
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

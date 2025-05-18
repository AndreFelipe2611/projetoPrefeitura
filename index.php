<?php
session_start();
require_once 'conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['usuario'];
    $senha = $_POST['senha'];

    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE nome = ?");
    $consulta->bind_param("s", $nome);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = $usuario['nome'];
            header("Location: ./pages/dashboard.php"); 
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container-login">
        <h2>Login</h2>
        <form method="POST">
            <select name="usuario" required>
                <option value="">Selecione o usuário</option>
                <?php
                // listar todos os usuários do banco
                $resultado = $conexao->query("SELECT nome FROM usuarios");
                while ($linha = $resultado->fetch_assoc()) {
                    echo "<option value=\"" . htmlspecialchars($linha['nome']) . "\">" . htmlspecialchars($linha['nome']) . "</option>";
                }
                ?>
            </select>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
            <?php if (isset($erro)) echo "<div class='mensagem-erro'>$erro</div>"; ?>
        </form>
    </div>
</body>
</html>

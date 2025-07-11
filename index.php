<?php
session_start();
require_once './conexao.php';

// Se o usuário já estiver logado, redireciona para a página correta
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario'] === 'ansal') {
        header("Location: ./pages/dashboard.php");
        exit();
    } elseif ($_SESSION['usuario'] === 'prefeitura') {
        header("Location: ./pages/dashboard2.php");
        exit();
    }
}


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

            if ($usuario['nome'] === 'ansal') {
                header("Location: ./pages/dashboard.php");
                exit();
            } elseif ($usuario['nome'] === 'prefeitura') {
                header("Location: ./pages/dashboard2.php");
                exit();
            } else {
                header("");
                exit();
            }

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
                $resultado_usuarios = $conexao->query("SELECT nome FROM usuarios ORDER BY nome ASC");
                while ($linha = $resultado_usuarios->fetch_assoc()) {
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
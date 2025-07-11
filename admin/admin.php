<?php
include("../conexao.php");

$mensagem = "";

// Função para sanitizar nomes de colunas (permitir letras, números e underline)
function sanitize_column_name($name) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

// Ações via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    
    
    //usuários
    
    if ($acao === 'adicionarUsuario' && isset($_POST['usuario'], $_POST['senha'])) {
        $usuario = trim($_POST['usuario']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $stmt = $conexao->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $senha);
        if ($stmt->execute()) {
            $mensagem = "Usuário adicionado com sucesso!";
        } else {
            $mensagem = "Erro ao adicionar usuário.";
        }
        $stmt->close();
    }

    elseif ($acao === 'editarUsuario' && isset($_POST['usuario_id'], $_POST['usuario_nome'])) {
        $id = (int)$_POST['usuario_id'];
        $nome = trim($_POST['usuario_nome']);
        if (!empty($nome)) {
            if (!empty($_POST['usuario_senha'])) {
                $senhaHash = password_hash($_POST['usuario_senha'], PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
                $stmt->bind_param("ssi", $nome, $senhaHash, $id);
            } else {
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
                $stmt->bind_param("si", $nome, $id);
            }
            if ($stmt->execute()) {
                $mensagem = "Usuário atualizado com sucesso!";
            } else {
                $mensagem = "Erro ao atualizar usuário.";
            }
            $stmt->close();
        } else {
            $mensagem = "Nome do usuário não pode estar vazio.";
        }
    }

    elseif ($acao === 'excluirUsuario' && isset($_POST['excluirUsuario'])) {
        $id = (int)$_POST['excluirUsuario'];
        $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensagem = "Usuário excluído com sucesso.";
        } else {
            $mensagem = "Erro ao excluir usuário.";
        }
        $stmt->close();
    }

    //------------------------------------------
    
    
    
    //colunas
    
    elseif ($acao === 'adicionarColuna' && isset($_POST['coluna'])) {
        $coluna = sanitize_column_name($_POST['coluna']);
        if ($coluna !== "") {
            $res = $conexao->query("SHOW COLUMNS FROM registros LIKE '$coluna'");
            if ($res && $res->num_rows === 0) {
                if ($conexao->query("ALTER TABLE registros ADD $coluna TEXT")) {
                    $mensagem = "Coluna '$coluna' adicionada com sucesso.";
                } else {
                    $mensagem = "Erro ao adicionar coluna.";
                }
            } else {
                $mensagem = "Coluna '$coluna' já existe.";
            }
        } else {
            $mensagem = "Nome da coluna inválido.";
        }
    }

    elseif ($acao === 'editarColuna' && isset($_POST['coluna_antiga'], $_POST['coluna_nova'])) {
        $colunaAntiga = sanitize_column_name($_POST['coluna_antiga']);
        $colunaNova = sanitize_column_name($_POST['coluna_nova']);
        if ($colunaAntiga !== "" && $colunaNova !== "") {
            $resAntiga = $conexao->query("SHOW COLUMNS FROM registros LIKE '$colunaAntiga'");
            $resNova = $conexao->query("SHOW COLUMNS FROM registros LIKE '$colunaNova'");
            if ($resAntiga && $resAntiga->num_rows > 0) {
                if ($resNova && $resNova->num_rows === 0) {
                    // Usar MODIFY para manter tipo TEXT e NULL por padrão
                    if ($conexao->query("ALTER TABLE registros CHANGE $colunaAntiga $colunaNova TEXT")) {
                        $mensagem = "Coluna renomeada de '$colunaAntiga' para '$colunaNova' com sucesso.";
                    } else {
                        $mensagem = "Erro ao renomear coluna.";
                    }
                } else {
                    $mensagem = "Nome '$colunaNova' já existe como coluna.";
                }
            } else {
                $mensagem = "Coluna '$colunaAntiga' não existe.";
            }
        } else {
            $mensagem = "Nomes de coluna inválidos.";
        }
    }

    elseif ($acao === 'excluirColuna' && isset($_POST['excluirColuna'])) {
        $coluna = sanitize_column_name($_POST['excluirColuna']);
        $res = $conexao->query("SHOW COLUMNS FROM registros LIKE '$coluna'");
        if ($res && $res->num_rows > 0) {
            if ($conexao->query("ALTER TABLE registros DROP COLUMN $coluna")) {
                $mensagem = "Coluna '$coluna' excluída com sucesso.";
            } else {
                $mensagem = "Erro ao excluir coluna.";
            }
        } else {
            $mensagem = "Coluna '$coluna' não existe.";
        }
    }

    //-------------------------------------------------------------

    //registros

    elseif ($acao === 'excluirRegistro' && isset($_POST['excluirRegistro'])) {
        $id = (int)$_POST['excluirRegistro'];
        $stmt = $conexao->prepare("DELETE FROM registros WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensagem = "Registro excluído com sucesso.";
        } else {
            $mensagem = "Erro ao excluir registro.";
        }
        $stmt->close();
    }
}

//------------------------------------------------------------

// Obter colunas
$colunas = [];
$resColunas = $conexao->query("SHOW COLUMNS FROM registros");
while ($linha = $resColunas->fetch_assoc()) {
    if ($linha['Field'] !== 'id') {
        $colunas[] = $linha['Field'];
    }
}

// Obter dados
$registros = $conexao->query("SELECT * FROM registros ORDER BY id DESC LIMIT 5");
$usuarios = $conexao->query("SELECT * FROM usuarios");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./admin.css">
    <title>Document</title>
</head>
<body>
</html>
<div class="container">
    <h1>Painel de Administração</h1>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <!-- Usuários -->
     <?php
     include("./subPagesAdmin/usuarios.php");
     ?>
    
    <!-- Colunas -->
     <?php
     include("./subPagesAdmin/colunas.php");
     ?>

    <!-- Registros -->
      <?php
     include("./subPagesAdmin/registros.php");
     ?>

</div>

<!-- Modal Editar Usuário -->
<div id="modalEditarUsuario" class="modal">
    <div class="modal-conteudo">
        <button class="modal-close" onclick="fecharModal('modalEditarUsuario')">&times;</button>
        <h3>Editar Usuário</h3>
        <form method="POST" id="formEditarUsuario">
            <input type="hidden" name="acao" value="editarUsuario" />
            <input type="hidden" name="usuario_id" id="usuario_id" />
            <label for="usuario_nome">Nome:</label>
            <input type="text" name="usuario_nome" id="usuario_nome" required />
            <label for="usuario_senha">Nova Senha (deixe em branco para manter a atual):</label>
            <input type="password" name="usuario_senha" id="usuario_senha" placeholder="Senha nova" />
            <button type="submit">Salvar</button>
            <button type="button" class="cancelar" onclick="fecharModal('modalEditarUsuario')">Cancelar</button>
        </form>
    </div>
</div>

<!-- Modal Editar Coluna -->
<div id="modalEditarColuna" class="modal">
    <div class="modal-conteudo">
        <button class="modal-close" onclick="fecharModal('modalEditarColuna')">&times;</button>
        <h3>Renomear Coluna</h3>
        <form method="POST" id="formEditarColuna">
            <input type="hidden" name="acao" value="editarColuna" />
            <input type="hidden" name="coluna_antiga" id="coluna_antiga" />
            <label for="coluna_nova">Novo nome da coluna:</label>
            <input type="text" name="coluna_nova" id="coluna_nova" required />
            <button type="submit">Salvar</button>
            <button type="button" class="cancelar" onclick="fecharModal('modalEditarColuna')">Cancelar</button>
        </form>
    </div>
</div>

<script src="./admin.js"></script>

</body>
</html>

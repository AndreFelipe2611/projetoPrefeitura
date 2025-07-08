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
    <meta charset="UTF-8" />
    <title>Painel de Administração Completo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .mensagem {
            background: #e0ffe0;
            border: 1px solid #88cc88;
            padding: 10px;
            margin-bottom: 20px;
            color: #2d662d;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background: #f0f0f0;
        }
        .form-inline input, .form-inline button {
            margin-right: 10px;
            padding: 8px;
            font-size: 1rem;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .btn.editar {
            background: #007bff;
            color: white;
        }
        .btn.excluir {
            background: #dc3545;
            color: white;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-conteudo {
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            width: 320px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .modal-conteudo h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        .modal-conteudo label {
            display: block;
            margin-top: 12px;
            font-weight: 600;
            color: #333;
        }
        .modal-conteudo input[type="text"],
        .modal-conteudo input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .modal-conteudo button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }
        .modal-conteudo button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        .modal-conteudo button.cancelar {
            background-color: #aaa;
            margin-top: 10px;
        }
        .modal-close {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 22px;
            font-weight: bold;
            color: #666;
            border: none;
            background: none;
            cursor: pointer;
        }
        .modal-close:hover {
            color: #000;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Painel de Administração</h1>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <!-- Usuários -->
    <section>
        <h2>Usuários</h2>
        <form method="POST" class="form-inline" style="margin-bottom:15px;">
            <input type="hidden" name="acao" value="adicionarUsuario" />
            <input type="text" name="usuario" placeholder="Nome do usuário" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <button type="submit" class="btn">Adicionar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Editar</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
            <?php while($usuario = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td>
                        <button class="btn editar" onclick="abrirModalEditarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars(addslashes($usuario['nome'])) ?>')">Editar</button>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
                            <input type="hidden" name="acao" value="excluirUsuario" />
                            <input type="hidden" name="excluirUsuario" value="<?= $usuario['id'] ?>" />
                            <button type="submit" class="btn excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Colunas -->
    <section style="margin-top:40px;">
        <h2>Colunas da tabela registros</h2>
        <form method="POST" class="form-inline" style="margin-bottom:15px;">
            <input type="hidden" name="acao" value="adicionarColuna" />
            <input type="text" name="coluna" placeholder="Nome da nova coluna" required />
            <button type="submit" class="btn">Adicionar Coluna</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome da Coluna</th>
                    <th>Editar (Renomear)</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($colunas as $coluna): ?>
                <tr>
                    <td><?= htmlspecialchars($coluna) ?></td>
                    <td>
                        <button class="btn editar" onclick="abrirModalEditarColuna('<?= htmlspecialchars($coluna) ?>')">Editar</button>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir a coluna <?= htmlspecialchars($coluna) ?>? Isso apagará todos os dados desta coluna.');">
                            <input type="hidden" name="acao" value="excluirColuna" />
                            <input type="hidden" name="excluirColuna" value="<?= htmlspecialchars($coluna) ?>" />
                            <button type="submit" class="btn excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Registros -->
    <section style="margin-top:40px;">
        <h2>Últimos 5 Registros</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <?php foreach($colunas as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
            <?php while($reg = $registros->fetch_assoc()): ?>
                <tr>
                    <td><?= $reg['id'] ?></td>
                    <?php foreach($colunas as $col): ?>
                        <td><?= htmlspecialchars($reg[$col]) ?></td>
                    <?php endforeach; ?>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir o registro #<?= $reg['id'] ?>?');">
                            <input type="hidden" name="acao" value="excluirRegistro" />
                            <input type="hidden" name="excluirRegistro" value="<?= $reg['id'] ?>" />
                            <button type="submit" class="btn excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>
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

<script>
function abrirModalEditarUsuario(id, nome) {
    document.getElementById('usuario_id').value = id;
    document.getElementById('usuario_nome').value = nome;
    document.getElementById('usuario_senha').value = "";
    abrirModal('modalEditarUsuario');
}

function abrirModalEditarColuna(nome) {
    document.getElementById('coluna_antiga').value = nome;
    document.getElementById('coluna_nova').value = nome;
    abrirModal('modalEditarColuna');
}

function abrirModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function fecharModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Fechar modais clicando fora da área do conteúdo
window.onclick = function(event) {
    const modais = ['modalEditarUsuario', 'modalEditarColuna'];
    modais.forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) {
            fecharModal(id);
        }
    });
}
</script>

</body>
</html>

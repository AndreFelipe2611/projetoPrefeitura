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
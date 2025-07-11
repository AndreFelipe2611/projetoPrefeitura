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
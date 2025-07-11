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
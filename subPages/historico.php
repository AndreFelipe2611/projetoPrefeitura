<div class="caixa-tabela" id="tabela-historico" style="display:none;">
    <table class="tabela-principal">
        <thead>
            <tr>
                <th>#</th>
                <?php foreach ($colunas_tabela as $coluna): ?>
                    <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                <?php endforeach; ?>
                <th>Anexo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
            while ($linha = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $linha['id']; ?></td>
                    <?php foreach ($colunas_tabela as $coluna): ?>
                        <td><?php echo htmlspecialchars($linha[$coluna] ?? ''); ?></td>
                    <?php endforeach; ?>
                    <td>
                        <?php if (!empty($linha['caminho_arquivo'])): ?>
                            <a href="../uploads/<?php echo htmlspecialchars($linha['caminho_arquivo']); ?>" target="_blank" class="acao visualizar" title="Ver anexo">
                                📎
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
                            
 <div id="tabela-historico" class="caixa-tabela" style="display:none;">
        <table class="tabela-registros">
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($colunas_tabela as $coluna): ?>
                        <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                    <?php endforeach; ?>
                    <th>Anexo</th>
                    <th>Concluído</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while ($linha = $result->fetch_assoc()):
                        $concluido = (int)($linha['concluido'] ?? 0);
                    ?>
                    <tr data-concluido="<?php echo $concluido ? 'sim' : 'nao'; ?>" data-id="<?php echo $linha['id']; ?>">
                        <td><?php echo $linha['id']; ?></td>
                        <?php foreach ($colunas_tabela as $coluna): ?>
                            <td><?php echo htmlspecialchars($linha[$coluna] ?? 'N/A'); ?></td>
                        <?php endforeach; ?>
                        <td>
                            <?php if (!empty($linha['caminhoArquivo'])): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($linha['caminhoArquivo']); ?>" target="_blank" class="acao-visualizar">
                                    Visualizar Anexo
                                </a>
                            <?php else: ?>
                                <span>Sem anexo</span>
                            <?php endif; ?>
                        </td>
                        <td class="coluna-checkbox">
                            <input type="checkbox" class="checkbox-concluido" <?php echo $concluido ? 'checked' : ''; ?> />
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="<?php echo count($colunas_tabela) + 3; ?>">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
 <!-- TABELA HISTÓRICO -->
        <div id="tabela-historico" class="caixa-tabela" style="display:none;">
            <table class="tabela-registros">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ($colunas_tabela as $coluna): ?>
                            <th><?php echo htmlspecialchars(ucfirst($coluna)); ?></th>
                        <?php endforeach; ?>
                        <th>Anexo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conexao->query("SELECT * FROM registros ORDER BY id DESC");
                    if ($result->num_rows > 0):
                        while ($linha = $result->fetch_assoc()):
                            $verificado = (isset($linha['concluido']) && $linha['concluido'] == 1);
                            $status = $verificado ? '✅ Verificado' : '❌ Não Verificado';
                    ?>
                        <tr data-status="<?php echo $verificado ? 'verificado' : 'nao-verificado'; ?>">
                            <td><?php echo $linha['id']; ?></td>
                            <?php foreach ($colunas_tabela as $coluna): ?>
                                <td><?php echo htmlspecialchars($linha[$coluna] ?? 'N/A'); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?php if (!empty($linha['caminhoArquivo']) || !empty($linha['caminho_arquivo'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($linha['caminhoArquivo'] ?: $linha['caminho_arquivo']); ?>" target="_blank" class="acao-visualizar">
                                        Visualizar Anexo
                                    </a>
                                <?php else: ?>
                                    <span>Sem anexo</span>
                                <?php endif; ?>
                            </td>
                            <td class="status-coluna"><?php echo $status; ?></td>
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
    </div>
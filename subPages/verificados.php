<?php
include("../conexao.php");

$sql = "SELECT * FROM registros WHERE status = 1 ORDER BY data DESC";
$resultado = $conexao->query($sql);
?>

<div class="caixa-tabela">
    <table class="tabela-registros">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Arquivo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($registro = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($registro['data']) ?></td>
                    <td><?= htmlspecialchars($registro['descricao']) ?></td>
                    <td>
                        <a class="acao-visualizar" href="<?= $registro['caminho_arquivo'] ?>" target="_blank">Visualizar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

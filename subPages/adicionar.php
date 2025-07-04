<div id="formulario-adicao" style="display:none; margin-top: 20px;">
    <form method="POST" action="../controles/salvarRegistro.php" enctype="multipart/form-data">
        <?php foreach ($colunas_tabela as $coluna): ?>
            <div>
                <label><?php echo htmlspecialchars(ucfirst($coluna)); ?>:</label>
                <input type="text" name="dados[<?php echo htmlspecialchars($coluna); ?>]" required>
            </div>
        <?php endforeach; ?>
        <div>
            <label>Enviar Arquivo/Foto:</label>
            <input type="file" name="arquivo" style="color: white;">
        </div>
        <button type="submit">Salvar</button>
    </form>
</div>

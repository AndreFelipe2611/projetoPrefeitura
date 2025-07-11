   <!-- FORMULÁRIO DE ADIÇÃO -->
        <div id="formulario-adicao" style="display:none; margin-top: 20px;">
            <form method="POST" action="../controles/salvarRegistro.php" enctype="multipart/form-data">
                <?php foreach ($colunas_tabela as $coluna): ?>
                    <div>
                        <label><?php echo ucfirst($coluna); ?>:</label>
                        <input type="text" name="dados[<?php echo $coluna; ?>]" required>
                    </div>
                <?php endforeach; ?>
                <div>
                    <label>Arquivo / Foto:</label>
                    <input type="file" name="arquivo" style="color: white;">
                </div>
                <button type="submit">Salvar</button>
            </form>
        </div>

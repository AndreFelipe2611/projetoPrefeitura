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
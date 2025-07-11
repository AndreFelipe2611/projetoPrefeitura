  function alternarAbas() {
            const abas = document.getElementById("abas");
            const botao = document.getElementById("botao-menu");
            abas.classList.toggle("mostrar");
            botao.classList.toggle("ativo");
        }

        function mostrarFormulario() {
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "block";
            document.getElementById("tabela-historico").style.display = "none";
        }

        function mostrarTabela() {
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "block";
            filtrarTabela('todos');
        }

        function filtrarVerificados() {
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "block";
            filtrarTabela('verificado');
        }

        function filtrarNaoVerificados() {
            document.getElementById("mensagem-boas-vindas").style.display = "none";
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "block";
            filtrarTabela('nao-verificado');
        }

        function filtrarTabela(filtro) {
            const linhas = document.querySelectorAll("#tabela-historico tbody tr");

            linhas.forEach(linha => {
                const status = linha.getAttribute("data-status");
                if (filtro === 'todos' || filtro === status) {
                    linha.style.display = "";
                } else {
                    linha.style.display = "none";
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("formulario-adicao").style.display = "none";
            document.getElementById("tabela-historico").style.display = "none";
            document.getElementById("mensagem-boas-vindas").style.display = "block";
        });
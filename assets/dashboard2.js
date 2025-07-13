 function alternarAbas() {
        const abas = document.getElementById("abas");
        const botao = document.getElementById("botao-menu");
        abas.classList.toggle("mostrar");
        botao.classList.toggle("ativo");
    }

    function mostrarTabela(filtro) {
        document.getElementById("mensagem-boas-vindas").style.display = "none";
        const linhas = document.querySelectorAll(".tabela-registros tbody tr");

        linhas.forEach(tr => {
            const status = tr.getAttribute("data-concluido");
            if (filtro === 'todos') {
                tr.style.display = "";
            } else {
                tr.style.display = (status === filtro) ? "" : "none";
            }
        });

        document.getElementById("tabela-historico").style.display = "block";
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Mensagem inicial
        document.getElementById("mensagem-boas-vindas").style.display = "flex";
        document.getElementById("tabela-historico").style.display = "none";

       
        document.querySelector(".tabela-registros tbody").addEventListener("change", function (e) {
            if (e.target.classList.contains("checkbox-concluido")) {
                const checkbox = e.target;
                const tr = checkbox.closest("tr");
                const id = tr.getAttribute("data-id");
                const novoStatus = checkbox.checked ? 1 : 0;

                tr.setAttribute("data-concluido", checkbox.checked ? "sim" : "nao");

                fetch("../controles/checkout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${encodeURIComponent(id)}&concluido=${encodeURIComponent(novoStatus)}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Resposta do servidor:", data);
                })
                .catch(err => {
                    alert("Erro ao salvar o status!");
                    console.error(err);
                });
            }
        });
    });
<?php
$servidor = "localhost";
$usuario = "root";
$senha = "afvm2611";
$banco = "prefeitura";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>

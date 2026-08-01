<?php
/**
 * BRINKEDUCA — Script Seguro de Reset Mensal via Cron-job.org
 */

// 1. MEDIDA DE SEGURANÇA: Verifica se a senha da URL está correta
// Se a pessoa acessar o link sem o token correto, o script para aqui.
$token_secreto = 'SenhaSecretaBrinkEduca123'; // Você pode mudar esta senha se quiser

if (!isset($_GET['token']) || $_GET['token'] !== $token_secreto) {
    die("Acesso negado. Token invalido.");
}

// 2. DADOS DE CONEXÃO DO INFINITYFREE
$host    = "sqlXXX.infinityfree.com"; // Substitua pelo seu host real
$usuario = "if0_XXXXXX";              // Substitua pelo seu usuário
$senha   = "SuaSenhaAqui";            // Substitua pela sua senha
$banco   = "if0_XXXXXX_brinkeduca";   // Substitua pelo nome do banco

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// 3. COMANDO SQL: Esvazia a tabela de rankings completamente
$sql = "TRUNCATE TABLE rankings";

if ($conn->query($sql) === TRUE) {
    echo "Sucesso: O ranking mensal do BrinkEduca foi resetado de forma segura!";
} else {
    echo "Erro ao resetar o ranking: " . $conn->error;
}

$conn->close();
?>
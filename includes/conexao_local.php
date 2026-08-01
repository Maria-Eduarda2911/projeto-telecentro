<?php

// ---------- (1) HOST DO BANCO ----------
// O InfinityFree usa um endereço tipo sqlXXX.infinityfree.com
define('DB_HOST', '127.0.0.1');

// ---------- (2) PORTA (opcional) ----------
// Normalmente 3306. Se o seu MySQL local usa outra porta, troque aqui.
define('DB_PORT', 3306);

// ---------- (3) USUÁRIO DO BANCO ----------
// Copiado exatamente do painel "MySQL Databases" do InfinityFree.
define('DB_USER', 'root');

// ---------- (4) SENHA DO BANCO ----------
// Copiada exatamente do painel. Cuidado: ela é case sensitive.
define('DB_PASS', '');

// ---------- (5) NOME DO BANCO DE DADOS ----------
// ⚠️ IMPORTANTE: Você precisa TROCAR a parte "brinkeduca" abaixo pelo
// nome EXATO que aparece em "Available database names" no painel do
// InfinityFree! Ex: se você criou como "quiz" vai ficar
// if0_42534198_quiz — olhe lá no painel antes de subir os arquivos.
define('DB_NAME', 'brinkeduca_db');


// ================================================================
// CONEXÃO PROCEDURAL SEGURA (compatível com servidores compartilhados)
// ================================================================
// Usamos @ para suprimir warnings e não expor a senha caso algo falhe.
// A conexão só é estabelecida UMA VEZ e é reutilizada pelo site todo.
$conexao = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

/**
 * Função auxiliar: retorna TRUE se a conexão com o banco está OK.
 * Usada no desafio.php para decidir se busca do MySQL ou usa o
 * fallback do array PHP (evita que o site quebre se o banco cair).
 */
function banco_conectado()
{
    global $conexao;
    return ($conexao instanceof mysqli && mysqli_connect_errno() === 0);
}

// Guarda mensagem de erro (se houver) para exibir em ambiente dev
$conexao_erro = mysqli_connect_error();

// ================================================================
// CONFIGURAÇÕES FINAIS — UTF-8 e time zone brasileiro
// ================================================================
if (banco_conectado()) {
    // Força UTF-8 (acentos portugueses, emojis)
    mysqli_set_charset($conexao, 'utf8mb4');

    // Evita warnings de time zone em servidores compartilhados
    @mysqli_query($conexao, "SET time_zone = '-03:00'");
}

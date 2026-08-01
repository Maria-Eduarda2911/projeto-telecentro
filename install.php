<?php
/**
 * INSTALL.PHP — Importa TODAS as questões do array PHP para o MySQL
 * --------------------------------------------------------------
 * 1. Configure os dados do banco em includes/conexao.php
 * 2. Rode o schema.sql no phpMyAdmin para criar as tabelas
 * 3. Abra esse arquivo no navegador: https://seusite/install.php
 * 4. Ele vai copiar TODAS as ~285 questões do array pro banco
 * 5. Depois APAGUE esse arquivo por segurança (no InfinityFree)
 */

require __DIR__ . '/includes/conexao_local.php';

echo '<h2>Brinkeduca — Instalador de Questões</h2>';

// 1. Verifica se o banco está conectado
if (!banco_conectado()) {
    echo '<p style="color:#c00;">❌ Erro na conexão MySQL: ' . htmlspecialchars($conexao_erro ?? 'desconhecido') . '</p>';
    echo '<p>Edite o arquivo includes/conexao.php com os dados corretos do seu banco.</p>';
    exit;
}
echo '<p style="color:#16a34a;">✅ Conectado no banco com sucesso.</p>';

// 2. Limpa a tabela de questões (evita duplicatas)
$ok = mysqli_query($conexao, 'TRUNCATE TABLE questoes');
if (!$ok) {
    echo '<p style="color:#c00;">❌ Erro ao limpar tabela: ' . mysqli_error($conexao) . '</p>';
    exit;
}
echo '<p>🧹 Tabela de questões limpa (vamos recriar tudo).</p>';

// 3. Carrega o array com todas as questões (mesmo usado antes da migração)
$desafios = require __DIR__ . '/includes/desafios_20.php';

$totalInseridas = 0;
$totalErros     = 0;

// 4. Percorre matérias (matematica, portugues, ingles)
foreach ($desafios as $slugMateria => $anos) {

    // Percorre cada ano (ano1..ano5)
    foreach ($anos as $slugAno => $dadosAno) {

        // Extrai o número do ano de "ano1" → 1
        $numeroAno = (int) str_replace('ano', '', $slugAno);

        // Cada pergunta do ano
        foreach ($dadosAno['questoes'] as $q) {

            // Prepara as opções A, B, C (todas as questoes tem 3 opcoes)
            $opcoes = $q['opcoes'] ?? ['', '', ''];
            $a = $opcoes[0] ?? '';
            $b = $opcoes[1] ?? '';
            $c = $opcoes[2] ?? '';

            // Escapa tudo para evitar SQL injection
            $materia    = mysqli_real_escape_string($conexao, $slugMateria);
            $pergunta   = mysqli_real_escape_string($conexao, $q['pergunta']);
            $opA        = mysqli_real_escape_string($conexao, $a);
            $opB        = mysqli_real_escape_string($conexao, $b);
            $opC        = mysqli_real_escape_string($conexao, $c);
            $resposta   = mysqli_real_escape_string($conexao, $q['resposta']);
            $explicacao = mysqli_real_escape_string($conexao, $q['explicacao']);
            $emoji      = mysqli_real_escape_string($conexao, $q['emoji'] ?? '❓');

            // Monta o INSERT (estilo procedural)
            $sql = "INSERT INTO questoes
                (materia, ano, pergunta, opcao_a, opcao_b, opcao_c, resposta, explicacao, emoji)
                VALUES
                ('$materia', $numeroAno, '$pergunta', '$opA', '$opB', '$opC', '$resposta', '$explicacao', '$emoji')";

            $inseriu = mysqli_query($conexao, $sql);

            if ($inseriu) {
                $totalInseridas++;
            } else {
                $totalErros++;
                echo '<p style="color:#c00;">Erro: ' . mysqli_error($conexao) . '</p>';
            }
        }
    }
}

// 5. Resultado final
echo '<hr>';
echo "<h3>Concluído! 🎉</h3>";
echo "<p>✅ Questões inseridas com sucesso: <strong>$totalInseridas</strong></p>";
if ($totalErros > 0) {
    echo "<p>❌ Erros: <strong>$totalErros</strong></p>";
}
echo '<p style="color:#dc2626;"><strong>⚠️ Por segurança, APAGUE o arquivo install.php do seu servidor agora.</strong></p>';
echo '<a href="index.php" style="background:#2563eb;color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;">→ Ir para o site</a>';

<?php
require('includes/conexao_local.php');
if (!banco_conectado()) { echo "Sem conexão."; exit; }

// A query agora garante que os dados vêm organizados
$sql = "SELECT nome, escola, pontuacao, total, materia, ano 
        FROM rankings 
        ORDER BY materia ASC, ano ASC, total DESC, pontuacao DESC";
$res = mysqli_query($conexao, $sql);

$ranking = [];
while($l = mysqli_fetch_assoc($res)) {
    // A chave agora inclui a matéria, o ano E a quantidade de perguntas
    $chave = ucfirst($l['materia']) . " | " . $l['ano'] . "º Ano | " . $l['total'] . " perguntas";
    $ranking[$chave][] = $l;
}

if (empty($ranking)) { echo "<p style='text-align:center;'>Nenhum desafio concluído ainda. Seja o primeiro!</p>"; exit; }

foreach ($ranking as $categoria => $jogadores) {
    // Título da categoria com um destaque visual
    echo '<h3 style="margin-top:25px; padding-bottom:5px; border-bottom:2px solid var(--primary); color:var(--primary);">'.$categoria.'</h3>';
    echo '<table style="width:100%; border-collapse:collapse; margin-bottom:10px;">';
    
    $pos = 1;
    foreach ($jogadores as $j) {
        $cor = ($pos <= 3) ? 'background:#FFF9D2;' : '';
        $emoji = ($pos == 1) ? '🥇 ' : (($pos == 2) ? '🥈 ' : (($pos == 3) ? '🥉 ' : ''));
        
        echo '<tr style="'.$cor.' border-bottom:1px solid #eee;">
                <td style="padding:10px; width:40px; font-weight:800;">'.$emoji.$pos.'º</td>
                <td style="padding:10px;">
                    <strong style="display:block;">'.htmlspecialchars($j['nome']).'</strong>
                    <small style="color:#666;">'.htmlspecialchars($j['escola']).'</small>
                </td>
                <td style="padding:10px; text-align:right;"><strong>'.$j['pontuacao'].' pts</strong></td>
              </tr>';
        $pos++;
    }
    echo '</table>';
}
?>
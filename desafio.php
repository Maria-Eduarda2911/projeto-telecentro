<?php
/**
 * ================================================================
 * DESAFIO.PHP — Página do quiz (versão MySQL + ranking + nome + escola)
 * ================================================================
 */

// ================================================================
// 1. SESSÃO
// ================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================================================================
// 2. INCLUDES OBRIGATÓRIOS
// ================================================================
$site    = require __DIR__ . '/includes/conteudo.php';
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/layout.php';
require('includes/conexao_local.php');

// ================================================================
// 3. CAPTURA DE PARÂMETROS DA URL
// ================================================================
$materiaSlug = $_GET['materia'] ?? '';
$ano         = ano_valido((int) ($_GET['ano'] ?? 1));
$quantidade  = 5;

// ================================================================
// 4. NOME E ESCOLA DA CRIANÇA (BLINDADO)
// ================================================================
$nomeCrianca   = '';
$escolaCrianca = '';

// 1. Tenta pegar do POST (formulário inicial)
if (isset($_POST['nome']) && trim($_POST['nome']) !== '') {
    $_SESSION['brink_nome'] = trim($_POST['nome']);
}
if (isset($_POST['escola']) && trim($_POST['escola']) !== '') {
    $_SESSION['brink_escola'] = trim($_POST['escola']);
}

// 2. Tenta pegar do GET (URL), MAS SÓ SALVA SE NÃO FOR VAZIO
if (isset($_GET['nome']) && trim($_GET['nome']) !== '') {
    $_SESSION['brink_nome'] = trim($_GET['nome']);
}
if (isset($_GET['escola']) && trim($_GET['escola']) !== '') {
    $_SESSION['brink_escola'] = trim($_GET['escola']);
}

// 3. Resgata da memória segura da sessão
if (!empty($_SESSION['brink_nome'])) {
    $nomeCrianca = $_SESSION['brink_nome'];
}
if (!empty($_SESSION['brink_escola'])) {
    $escolaCrianca = $_SESSION['brink_escola'];
}

// 4. Se mesmo assim estiver vazio, aplica o padrão
if (empty($escolaCrianca)) {
    $escolaCrianca = 'Não informada';
}

// 5. Sanitização final
if (!empty($nomeCrianca)) {
    $nomeCrianca = htmlspecialchars(mb_substr($nomeCrianca, 0, 80), ENT_QUOTES);
}
if ($escolaCrianca !== 'Não informada') { 
    $escolaCrianca = htmlspecialchars(mb_substr($escolaCrianca, 0, 100), ENT_QUOTES);
}

// ================================================================
// 5. VALIDAÇÃO DA MATÉRIA E ANO
// ================================================================
$materia  = $site['materias'][$materiaSlug] ?? null;
$anoChave = 'ano' . $ano;
$dadosAno = $materia['anos'][$anoChave] ?? null;

if ($materia === null || $dadosAno === null) {
    render_header($site, 'Desafio não encontrado', 'Essa matéria não existe. Volte e escolha outra.');
    ?>
    <section class="notice-card">
        <h2>Não encontramos esse desafio</h2>
        <p>Volte à página inicial e escolha Matemática, Português ou Inglês.</p>
        <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
    </section>
    <?php
    render_footer();
    exit;
}

// ================================================================
// 6. CARREGA AS 5 QUESTÕES (MySQL com fallback)
// ================================================================
$questoes        = [];
$indicesSorteados = [];
$usouBanco       = banco_conectado();

if ($usouBanco) {
    $materiaSQL = mysqli_real_escape_string($conexao, $materiaSlug);
    $anoSQL     = (int) $ano;

    $sql = "SELECT id, pergunta, opcao_a, opcao_b, opcao_c, resposta, explicacao, emoji
            FROM questoes
            WHERE materia = '$materiaSQL' AND ano = $anoSQL
            ORDER BY RAND()
            LIMIT 5";

    $resultado = mysqli_query($conexao, $sql);

    if ($resultado && mysqli_num_rows($resultado) >= 1) {
        while ($linha = mysqli_fetch_assoc($resultado)) {
            $questoes[] = [
                'id'         => (int) $linha['id'],
                'pergunta'   => $linha['pergunta'],
                'opcoes'     => [$linha['opcao_a'], $linha['opcao_b'], $linha['opcao_c']],
                'resposta'   => $linha['resposta'],
                'explicacao' => $linha['explicacao'],
                'emoji'      => !empty($linha['emoji']) ? $linha['emoji'] : '❓',
            ];
            $indicesSorteados[] = (int) $linha['id'];
        }
    }

    if (count($questoes) < 1) {
        $usouBanco = false;
    }
}

if (!$usouBanco) {
    $todasQuestoes = $dadosAno['questoes'] ?? [];
    $indicesSorteados = array_keys($todasQuestoes);
    shuffle($indicesSorteados);
    $indicesSorteados = array_slice($indicesSorteados, 0, $quantidade);

    foreach ($indicesSorteados as $idx) {
        if (isset($todasQuestoes[$idx])) {
            $q = $todasQuestoes[$idx];
            $q['id'] = $idx;
            $questoes[] = $q;
        }
    }
}

$total   = count($questoes);
$acertos = 0;
$feedback = [];
$avaliado = ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['nome_usuario']));

// ================================================================
// 7. RECUPERAR A ORDEM DAS QUESTÕES NO SUBMIT (POST)
// ================================================================
if ($avaliado && !empty($_POST['indices'])) {
    $idsRecebidos = json_decode($_POST['indices'], true);
    if (is_array($idsRecebidos) && !empty($idsRecebidos)) {
        $questoes = [];
        if ($usouBanco && banco_conectado()) {
            foreach ($idsRecebidos as $idQ) {
                $idQ  = (int) $idQ;
                $sql  = "SELECT id, pergunta, opcao_a, opcao_b, opcao_c, resposta, explicacao, emoji
                         FROM questoes WHERE id = $idQ LIMIT 1";
                $res  = mysqli_query($conexao, $sql);
                if ($res && $linha = mysqli_fetch_assoc($res)) {
                    $questoes[] = [
                        'id'         => (int) $linha['id'],
                        'pergunta'   => $linha['pergunta'],
                        'opcoes'     => [$linha['opcao_a'], $linha['opcao_b'], $linha['opcao_c']],
                        'resposta'   => $linha['resposta'],
                        'explicacao' => $linha['explicacao'],
                        'emoji'      => !empty($linha['emoji']) ? $linha['emoji'] : '❓',
                    ];
                }
            }
        } else {
            $todas = $dadosAno['questoes'] ?? [];
            foreach ($idsRecebidos as $idQ) {
                if (isset($todas[$idQ])) {
                    $q = $todas[$idQ];
                    $q['id'] = $idQ;
                    $questoes[] = $q;
                }
            }
        }
        $total = count($questoes);
    }
}

// ================================================================
// 8. CORREÇÃO DAS RESPOSTAS
// ================================================================
$respostas = $_POST['respostas'] ?? [];

if ($avaliado) {
    foreach ($questoes as $indice => $questao) {
        $respostaUsuario = isset($respostas[$indice]) ? (string) $respostas[$indice] : '';
        $correta         = $questao['resposta'];
        $acertou         = ($respostaUsuario === $correta);

        if ($acertou) {
            $acertos++;
        }

        $feedback[] = [
            'pergunta'        => $questao['pergunta'],
            'opcoes'          => $questao['opcoes'],
            'resposta_usuario' => $respostaUsuario,
            'correta'         => $correta,
            'explicacao'      => $questao['explicacao'],
            'emoji'           => $questao['emoji'] ?? '❓',
            'acertou'         => $acertou,
        ];
    }

    // ============================================================
    // 9. INSERE A PONTUAÇÃO NA TABELA `rankings` — [RESTAURADO]
    // ============================================================
    if (!empty($nomeCrianca) && $nomeCrianca !== 'Anônimo' && banco_conectado()) {
        $nomeSQL     = mysqli_real_escape_string($conexao, $nomeCrianca);
        $escolaSQL   = mysqli_real_escape_string($conexao, $escolaCrianca);
        $materiaSQL  = mysqli_real_escape_string($conexao, $materiaSlug);
        $pontuacao   = (int) $acertos;
        $totalQ      = (int) $total;
        $anoI        = (int) $ano;

        $sqlRanking = "INSERT INTO rankings (nome, escola, pontuacao, total, materia, ano) 
                       VALUES ('$nomeSQL', '$escolaSQL', $pontuacao, $totalQ, '$materiaSQL', $anoI)";
        @mysqli_query($conexao, $sqlRanking);
    }
}

// ================================================================
// 10. CARREGA O QUADRO DE HONRA (top 10) — [CORRIGIDO]
// ================================================================
$quadroHonra = [];
if (banco_conectado()) {
    // Removido 'escola' do SELECT para não quebrar no banco local
    // Seção 10: Buscando a coluna 'escola' também
$sqlHonra = "SELECT nome, escola, pontuacao, total, materia, ano, data
             FROM rankings
             ORDER BY pontuacao DESC, data DESC
             LIMIT 10";
    $resHonra = mysqli_query($conexao, $sqlHonra);
    if ($resHonra && mysqli_num_rows($resHonra) > 0) {
        while ($linha = mysqli_fetch_assoc($resHonra)) {
            $quadroHonra[] = $linha;
        }
    }
}

// ================================================================
// 11. TÍTULO DA PÁGINA E CABEÇALHO
// ================================================================
$tituloPagina = $materia['titulo'] . ' — ' . $ano . 'º ano — 5 perguntas';
$subtitulo    = $dadosAno['introducao'] ?? $materia['descricao'];
render_header($site, $tituloPagina, $subtitulo);
?>

<section class="challenge-summary">
    <div>
        <p class="eyebrow"><?= e($materia['titulo']) ?> — <?= e((string) $ano) ?>º ano</p>
        <h2>5 perguntas sorteadas</h2>
        <p>
            <?php if (!empty($nomeCrianca)): ?>
                Olá, <strong><?= e($nomeCrianca) ?></strong><?php if(!empty($escolaCrianca)): ?> da escola <strong><?= e($escolaCrianca) ?></strong><?php endif; ?>! Leia com calma, escolha uma resposta por pergunta e boa sorte! 🍀
            <?php else: ?>
                Leia com calma, escolha uma resposta por pergunta e boa sorte! 🍀
            <?php endif; ?>
        </p>
    </div>
    <div class="actions-row">
        <?php if (!empty($nomeCrianca)): ?>
            <a class="button secondary" href="<?= e(desafio_url($materiaSlug, 5, $ano)) ?>&nome=&escola=">
                👤 Trocar questões
            </a>
        <?php endif; ?>
        <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
    </div>
</section>

<?php

// Verifica se o aluno está logado antes de exibir o quiz ou resultados
if (empty($nomeCrianca)): ?>
    <section class="notice-card" style="border-top: 4px solid var(--warning);">
        <h2>Atenção!</h2>
        <p>Você precisa informar seu nome e escola no painel acima para participar do desafio e registrar seus pontos. 😊</p>
    </section>
<?php else: ?>

    <?php if (!$avaliado): ?>
    <div class="progress-panel">
        <div class="progress-info">
            <span class="progress-label">Progresso</span>
            <span class="progress-counter"><span id="progressCount">0</span> de <?= (int) $total ?></span>
        </div>
        <div class="progress-bar-wrap">
            <div id="progressBar" class="progress-bar-fill" style="width: 0%"></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($avaliado): ?>
        <section class="result-card scoreboard">
            <div class="scoreboard-top">
                <div class="scoreboard-emoji"><?= e(emoji_desempenho($acertos, $total)) ?></div>
                <div>
                    <p class="eyebrow">Seu resultado, <?= e($nomeCrianca) ?></p>
                    <h2 class="scoreboard-title"><?= e(classificar_desempenho($acertos, $total)) ?></h2>
                    <p class="score">
                        <span class="score-number"><?= (int) $acertos ?></span>
                        <span class="score-sep">de</span>
                        <span class="score-total"><?= (int) $total ?></span>
                        <span class="score-label">acertos</span>
                    </p>
                    <div class="score-percent-wrap">
                        <div class="score-percent-bar" style="width: <?= e((string) percentual($acertos, $total)) ?>%"></div>
                    </div>
                </div>
            </div>
        <?php
        $msgBase = mensagem_motivacional($acertos, $total);
        if (!empty($nomeCrianca)) {
            $msgBase = e($nomeCrianca) . ', ' . mb_strtolower(mb_substr($msgBase, 0, 1)) . mb_substr($msgBase, 1);
        }
        ?>
        <p class="motivational"><?= $msgBase ?></p>

        <?php
        $percentual = percentual($acertos, $total);
        $estrelas   = 0;
        if ($percentual >= 90) $estrelas = 3;
        elseif ($percentual >= 60) $estrelas = 2;
        elseif ($percentual >= 30) $estrelas = 1;
        ?>
        <div class="stars-row" aria-label="<?= (int) $estrelas ?> de 3 estrelas">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <span class="star <?= $i <= $estrelas ? 'on' : 'off' ?>" aria-hidden="true"><?= $i <= $estrelas ? '⭐' : '☆' ?></span>
            <?php endfor; ?>
        </div>

        <div class="actions-row">
            <a class="button primary" id="playAgainBtn" href="<?= e(desafio_url($materiaSlug, 5, $ano)) ?><?= !empty($nomeCrianca) ? '&nome=' . urlencode($nomeCrianca) : '' ?><?= !empty($escolaCrianca) ? '&escola=' . urlencode($escolaCrianca) : '' ?>">
                🎮 Jogar Novamente
            </a>
            <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
        </div>
    </section>

    <section class="review-section">
        <div class="review-header">
            <h2 class="review-title">📚 Modo Revisão</h2>
            <p class="review-subtitle">Veja o que você acertou e onde pode melhorar</p>
        </div>
        <div class="feedback-list">
            <?php foreach ($feedback as $indice => $item): ?>
                <article class="answer-card review-card <?= $item['acertou'] ? 'ok' : 'wrong' ?>">
                    <div class="review-card-head">
                        <div class="question-badge <?= $item['acertou'] ? 'badge-ok' : 'badge-wrong' ?>">
                            <?= $item['acertou'] ? '✓' : '✗' ?>
                        </div>
                        <div class="review-card-title">
                            <p class="question-index">Pergunta <?= (int) ($indice + 1) ?> de <?= (int) $total ?></p>
                            <h3><?= e($item['pergunta']) ?></h3>
                        </div>
                        <div class="review-emoji"><?= e($item['emoji']) ?></div>
                    </div>
                    <div class="review-options">
                        <?php foreach ($item['opcoes'] as $opcao):
                            $isCorreta       = $opcao === $item['correta'];
                            $isEscolhidaErrada = ($opcao === $item['resposta_usuario'] && !$item['acertou']);
                            $classe = 'review-option';
                            if ($isCorreta)       $classe .= ' opt-correct';
                            if ($isEscolhidaErrada) $classe .= ' opt-wrong';
                            ?>
                            <div class="<?= $classe ?>">
                                <span class="opt-mark" aria-hidden="true">
                                    <?php if ($isCorreta): ?>✓<?php elseif ($isEscolhidaErrada): ?>✗<?php else: ?>•<?php endif; ?>
                                </span>
                                <span class="opt-text"><?= e($opcao) ?></span>
                                <?php if ($isCorreta): ?>
                                    <span class="opt-tag tag-correct">Resposta certa</span>
                                <?php elseif ($isEscolhidaErrada): ?>
                                    <span class="opt-tag tag-wrong">Você escolheu</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="review-explanation">
                        <strong>💡 Explicação:</strong> <?= e($item['explicacao']) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (!empty($quadroHonra)): ?>
    <section class="result-card" style="padding:26px;">
        <div class="review-header" style="margin-bottom:18px;">
            <h2 class="review-title">🏆 Quadro de Honra — Top 10</h2>
            <p class="review-subtitle">As melhores pontuações de todas as crianças que já jogaram!</p>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
                <thead>
                    <tr style="background:var(--primary-soft);">
                        <th style="padding:12px; text-align:left; border-radius:10px 0 0 0; color:var(--primary); font-weight:700;">#</th>
                        <th style="padding:12px; text-align:left; color:var(--primary); font-weight:700;">Nome</th>
                        <th style="padding:12px; text-align:left; color:var(--primary); font-weight:700;">Escola</th> <th style="padding:12px; text-align:center; color:var(--primary); font-weight:700;">Pontos</th>
                        <th style="padding:12px; text-align:left; color:var(--primary); font-weight:700;">Matéria</th>
                        <th style="padding:12px; text-align:left; border-radius:0 10px 0 0; color:var(--primary); font-weight:700;">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quadroHonra as $i => $l): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px; font-weight:800; color:
                            <?php if ($i === 0) echo '#b45309';
                                  elseif ($i === 1) echo '#475569';
                                  elseif ($i === 2) echo '#9a3412';
                                  else echo 'var(--muted);'; ?>">
                            <?php if ($i === 0) echo '🥇';
                                  elseif ($i === 1) echo '🥈';
                                  elseif ($i === 2) echo '🥉';
                                  else echo (int) ($i + 1) . 'º'; ?>
                        </td>
                        <td style="padding:12px; font-weight:600;"><?= e($l['nome']) ?></td>
                        <td style="padding:12px; color:var(--text); font-size:0.9rem;"><?= !empty($l['escola'] ?? '') ? e($l['escola']) : '<span style="color:var(--muted);">Não informada</span>' ?></td> <td style="padding:12px; text-align:center;">
                            <strong style="color:var(--success); font-size:1.1rem;"><?= (int) $l['pontuacao'] ?></strong>
                            <span style="color:var(--muted);">/<?= (int) $l['total'] ?></span>
                        </td>
                        <td style="padding:12px;"><?= e(ucfirst($l['materia'])) ?> — <?= (int) $l['ano'] ?>º ano</td>
                        <td style="padding:12px; color:var(--muted); font-size:0.85rem;">
                            <?= date('d/m/Y H:i', strtotime($l['data'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

<?php else: ?>
    <form class="quiz-form" method="post" action="<?= e(desafio_url($materiaSlug, 5, $ano)) ?><?= !empty($nomeCrianca) ? '&nome=' . urlencode($nomeCrianca) : '' ?><?= !empty($escolaCrianca) ? '&escola=' . urlencode($escolaCrianca) : '' ?>" id="quizForm">

        <input type="hidden" name="indices" value="<?= e(json_encode($indicesSorteados, JSON_UNESCAPED_UNICODE)) ?>">

        <?php foreach ($questoes as $indice => $questao): ?>
            <fieldset class="question-card" data-question="<?= (int) $indice ?>">
                <legend>Pergunta <?= (int) ($indice + 1) ?> de <?= (int) $total ?></legend>
                <div class="question-media">
                    <span class="question-emoji"><?= e($questao['emoji'] ?? '❓') ?></span>
                </div>
                <h3><?= e($questao['pergunta']) ?></h3>
                <div class="options-grid" data-options>
                    <?php foreach ($questao['opcoes'] as $opcao): ?>
                        <label class="option-item" data-option>
                            <input
                                type="radio"
                                name="respostas[<?= (int) $indice ?>]"
                                value="<?= e($opcao) ?>"
                                data-correct="<?= ($opcao === $questao['resposta']) ? '1' : '0' ?>"
                                data-question-index="<?= (int) $indice ?>"
                                class="option-input"
                            >
                            <span class="option-marker" aria-hidden="true"></span>
                            <span class="option-label"><?= e($opcao) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>

        <div class="submit-box">
            <button class="button primary" type="submit" id="submitBtn">
                <span>Ver resultado</span>
                <span class="btn-icon">🏁</span>
            </button>
        </div>
    </form>
<?php endif; ?>
<?php endif; ?>

<script>
(function(){
    "use strict";

    var STORAGE_KEY_RECORD = 'brinkeduca_recorde_' + (<?= json_encode($materiaSlug . '_' . $ano) ?>);
    var SOM_ATIVADO = true;

    var audioCtx = null;
    function getAudioCtx(){
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
            catch(e){ audioCtx = null; }
        }
        return audioCtx;
    }

    function tocarTom(frequencias, duracao, tipo, volume){
        if (!SOM_ATIVADO) return;
        var ctx = getAudioCtx();
        if (!ctx) return;
        var now = ctx.currentTime;
        var ganho = ctx.createGain();
        ganho.gain.setValueAtTime(0, now);
        ganho.connect(ctx.destination);
        frequencias.forEach(function(freq, idx){
            var osc = ctx.createOscillator();
            osc.type = tipo || 'sine';
            osc.frequency.value = freq;
            var tempoInicio = now + idx * (duracao / frequencias.length) * 0.5;
            ganho.gain.setValueAtTime(0, tempoInicio);
            ganho.gain.linearRampToValueAtTime(volume || 0.15, tempoInicio + 0.02);
            ganho.gain.exponentialRampToValueAtTime(0.0001, tempoInicio + duracao / frequencias.length);
            osc.connect(ganho);
            osc.start(tempoInicio);
            osc.stop(tempoInicio + duracao / frequencias.length + 0.05);
        });
    }

    function somAcerto(){ tocarTom([523.25, 659.25, 783.99, 1046.50], 0.45, 'sine', 0.18); }
    function somErro(){  tocarTom([330, 277, 220], 0.4, 'triangle', 0.12); }
    function somSelecionar(){ tocarTom([660], 0.08, 'sine', 0.08); }
    function somFinalizar(){ tocarTom([523, 659, 784, 1046, 1318], 0.6, 'triangle', 0.15); }

    <?php if (!$avaliado): ?>
    var form = document.getElementById('quizForm');
    var progressBar = document.getElementById('progressBar');
    var progressCount = document.getElementById('progressCount');
    var totalQuestoes = <?= (int) $total ?>;
    var respondidas = new Set();

    function atualizarProgresso(){
        var pct = Math.round((respondidas.size / totalQuestoes) * 100);
        if (progressBar) progressBar.style.width = pct + '%';
        if (progressCount) progressCount.textContent = respondidas.size;
    }

    if (form) {
        form.addEventListener('change', function(e){
            var input = e.target.closest('input[type="radio"]');
            if (!input) return;
            var qIndex = input.getAttribute('data-question-index');
            var card = input.closest('.question-card');
            var opcoes = card ? card.querySelectorAll('.option-item') : [];
            opcoes.forEach(function(opt){ opt.classList.remove('is-selected'); });
            input.closest('.option-item').classList.add('is-selected');
            if (!respondidas.has(qIndex)) {
                respondidas.add(qIndex);
                somSelecionar();
                atualizarProgresso();
            } else {
                somSelecionar();
            }
        });
        form.addEventListener('submit', function(){ somFinalizar(); });
    }
    <?php endif; ?>

    <?php if ($avaliado): ?>
    var acertos = <?= (int) $acertos ?>;
    var total = <?= (int) $total ?>;
    var recordeAntigo = 0;
    try {
        var salvo = localStorage.getItem(STORAGE_KEY_RECORD);
        if (salvo !== null) recordeAntigo = parseInt(salvo, 10) || 0;
    } catch(e){}

    if (acertos > recordeAntigo) {
        try { localStorage.setItem(STORAGE_KEY_RECORD, String(acertos)); } catch(e){}
        var rr = document.getElementById('recordRow');
        if (rr) rr.style.display = 'flex';
        tocarTom([880, 1108.73, 1318.51, 1567.98], 0.7, 'sine', 0.2);
    }

    var opcoesRevisao = document.querySelectorAll('.review-card');
    opcoesRevisao.forEach(function(card, i){
        var isOk = card.classList.contains('ok');
        setTimeout(function(){ if (isOk) somAcerto(); else somErro(); }, 50 + i * 80);
    });

    var playAgain = document.getElementById('playAgainBtn');
    if (playAgain) {
        playAgain.addEventListener('click', function(){ tocarTom([523, 784, 1046], 0.3, 'sine', 0.15); });
    }
    <?php endif; ?>

    document.addEventListener('keydown', function(e){
        if (e.key && e.key.toLowerCase() === 'm') SOM_ATIVADO = !SOM_ATIVADO;
    });
})();
</script>

<?php render_footer(); ?>
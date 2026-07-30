<?php

$site = require __DIR__ . '/includes/conteudo.php';
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/layout.php';

$materiaSlug = $_GET['materia'] ?? '';
$quantidade = quantidade_valida((int) ($_GET['q'] ?? 5));
$ano = ano_valido((int) ($_GET['ano'] ?? 1));

$materia = $site['materias'][$materiaSlug] ?? null;
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

$questoes = array_slice($dadosAno['questoes'] ?? [], 0, $quantidade);
$respostas = $_POST['respostas'] ?? [];
$avaliado = $_SERVER['REQUEST_METHOD'] === 'POST';
$total = count($questoes);
$acertos = 0;
$feedback = [];

if ($avaliado) {
    foreach ($questoes as $indice => $questao) {
        $respostaUsuario = isset($respostas[$indice]) ? (string) $respostas[$indice] : '';
        $correta = $questao['resposta'];
        $acertou = $respostaUsuario === $correta;

        if ($acertou) {
            $acertos++;
        }

        $feedback[] = [
            'pergunta' => $questao['pergunta'],
            'resposta_usuario' => $respostaUsuario,
            'correta' => $correta,
            'explicacao' => $questao['explicacao'],
            'acertou' => $acertou,
        ];
    }
}

$tituloPagina = $materia['titulo'] . ' — ' . $ano . 'º ano — ' . $quantidade . ' perguntas';
$subtitulo = $dadosAno['introducao'] ?? $materia['descricao'];
render_header($site, $tituloPagina, $subtitulo);
?>

<section class="challenge-summary">
    <div>
        <p class="eyebrow"><?= e($materia['titulo']) ?> — <?= e((string) $ano) ?>º ano</p>
        <h2><?= e((string) $quantidade) ?> perguntas</h2>
        <p>Leia com calma, escolha uma resposta por pergunta e corrija no final.</p>
    </div>
    <div class="actions-row">
        <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
    </div>
</section>

<section class="difficulty-panel">
    <details class="wp-filter-panel">
        <summary>Quantas perguntas você quer responder?</summary>
        <div class="filter-body">
            <div class="level-grid">
                <a class="button <?= $quantidade === 5 ? 'primary' : 'secondary' ?>" href="<?= e(desafio_url($materiaSlug, 5, $ano)) ?>">5 perguntas</a>
                <a class="button <?= $quantidade === 10 ? 'primary' : 'secondary' ?>" href="<?= e(desafio_url($materiaSlug, 10, $ano)) ?>">10 perguntas</a>
                <a class="button <?= $quantidade === 15 ? 'primary' : 'secondary' ?>" href="<?= e(desafio_url($materiaSlug, 15, $ano)) ?>">15 perguntas</a>
                <a class="button <?= $quantidade === 20 ? 'primary' : 'secondary' ?>" href="<?= e(desafio_url($materiaSlug, 20, $ano)) ?>">20 perguntas</a>
            </div>
        </div>
    </details>
</section>

<?php if ($avaliado): ?>
    <section class="result-card">
        <p class="eyebrow">Seu resultado</p>
        <h2><?= e(classificar_desempenho($acertos, $total)) ?></h2>
        <p class="score"><?= (int) $acertos ?> de <?= (int) $total ?> acertos</p>
        <div class="actions-row">
            <a class="button primary" href="<?= e(desafio_url($materiaSlug, $quantidade, $ano)) ?>">Tentar de novo</a>
            <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
        </div>
    </section>

    <section class="feedback-list">
        <?php foreach ($feedback as $indice => $item): ?>
            <article class="answer-card <?= $item['acertou'] ? 'ok' : 'wrong' ?>">
                <p class="question-index">Pergunta <?= (int) ($indice + 1) ?></p>
                <h3><?= e($item['pergunta']) ?></h3>
                <p><strong>Você respondeu:</strong> <?= e($item['resposta_usuario'] !== '' ? $item['resposta_usuario'] : 'não respondeu') ?></p>
                <p><strong>Resposta certa:</strong> <?= e($item['correta']) ?></p>
                <p><?= e($item['explicacao']) ?></p>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <form class="quiz-form" method="post" action="<?= e(desafio_url($materiaSlug, $quantidade, $ano)) ?>">
        <?php foreach ($questoes as $indice => $questao): ?>
            <fieldset class="question-card">
                <legend>Pergunta <?= (int) ($indice + 1) ?> de <?= (int) $total ?></legend>
                <div class="question-media">
                    <span class="question-emoji"><?= e($questao['emoji'] ?? '❓') ?></span>
                </div>
                <h3><?= e($questao['pergunta']) ?></h3>
                <div class="options-grid">
                    <?php foreach ($questao['opcoes'] as $opcao): ?>
                        <label class="option-item">
                            <input
                                type="radio"
                                name="respostas[<?= (int) $indice ?>]"
                                value="<?= e($opcao) ?>"
                                <?php if (($respostas[$indice] ?? '') === $opcao): ?>checked<?php endif; ?>
                            >
                            <span><?= e($opcao) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>

        <div class="submit-box">
            <button class="button primary" type="submit">Ver resultado</button>
        </div>
    </form>
<?php endif; ?>

<?php render_footer(); ?>

<?php

$site = require __DIR__ . '/includes/conteudo.php';
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/layout.php';

render_header($site, 'Desafios educativos', 'Escolha a matéria e a quantidade de perguntas. Sem pressa — cada criança no seu ritmo.', false);
?>

<section class="intro-card">
    <p>Aqui você encontra desafios de Matemática, Português e Inglês, prontos para usar no Telecentro ou em sala. Tudo direto, sem distrações.</p>
    <div class="actions-row">
        <a class="button primary" href="jogos.php">Atividades Plugadas</a>
        <a class="button secondary" href="desplugadas.php">Atividades desplugadas</a>
    </div>
</section>

<section class="notice-card">
    <p class="eyebrow">Para o Telecentro de Olinda</p>
    <h2>Feito para quem ensina e para quem aprende</h2>
    <p>Um espaço simples, acolhedor e fácil de navegar — pensado junto com a realidade de quem trabalha com crianças de 5 a 12 anos.</p>
</section>

<section class="year-grid">
    <?php foreach (range(1, 5) as $ano): ?>
        <details class="subject-card subject-accordion">
            <summary>
                <div class="subject-head">
                    <div class="subject-icon" aria-hidden="true">📘</div>
                    <div>
                        <h2><?= e($ano) ?>º ano</h2>
                        <p>Questões de Matemática, Português e Inglês com 5, 10, 15 ou 20 perguntas.</p>
                        <span class="accordion-hint">Escolha a matéria e a quantidade</span>
                    </div>
                </div>
            </summary>

            <div class="subjects-list">
                <?php foreach ($site['materias'] as $slug => $materia): ?>
                    <div class="subject-row">
                        <div>
                            <h3><?= e($materia['titulo']) ?></h3>
                            <p><?= e($materia['descricao']) ?></p>
                        </div>
                        <div class="level-grid">
                            <a class="button primary" href="<?= e(desafio_url($slug, 5, $ano)) ?>">5 perguntas</a>
                            <a class="button secondary" href="<?= e(desafio_url($slug, 10, $ano)) ?>">10 perguntas</a>
                            <a class="button secondary" href="<?= e(desafio_url($slug, 15, $ano)) ?>">15 perguntas</a>
                            <a class="button secondary" href="<?= e(desafio_url($slug, 20, $ano)) ?>">20 perguntas</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endforeach; ?>
</section>

<?php render_footer(); ?>

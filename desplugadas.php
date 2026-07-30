<?php

$site = require __DIR__ . '/includes/conteudo.php';
$desplugadas = require __DIR__ . '/includes/desplugadas.php';
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/layout.php';

render_header($site, 'Atividades desplugadas', 'Propostas sem tela para Matemática, Português, Informática, Leitura e Pintura.');
?>

<section class="intro-card">
    <p>Atividades para fazer na escola ou em casa, com materiais simples. Escolha a área e explore.</p>
    <div class="actions-row">
        <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
        <a class="button primary" href="jogos.php">Ver jogos</a>
    </div>
</section>

<?php foreach ($desplugadas as $grupo): ?>
    <section class="subject-card">
        <div class="subject-head">
            <div class="subject-icon" aria-hidden="true"><?= e($grupo['icone']) ?></div>
            <div>
                <h2><?= e($grupo['titulo']) ?></h2>
                <p><?= e($grupo['descricao']) ?></p>
            </div>
        </div>

        <?php if (!empty($grupo['portais'])): ?>
            <div class="portals-grid">
                <?php foreach ($grupo['portais'] as $portal): ?>
                    <article class="portal-card">
                        <h3><?= e($portal['titulo']) ?></h3>
                        <p><?= e($portal['descricao']) ?></p>
                        <a class="button primary" href="<?= e($portal['url']) ?>" target="_blank" rel="noopener noreferrer">Acessar portal</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($grupo['ideias'])): ?>
            <div class="ideas-grid">
                <?php foreach ($grupo['ideias'] as $ideia): ?>
                    <article class="idea-card">
                        <p class="eyebrow"><?= e($ideia['faixa']) ?></p>
                        <h3><?= e($ideia['titulo']) ?></h3>
                        <p><?= e($ideia['descricao']) ?></p>
                        <p class="idea-meta"><strong>Materiais:</strong> <?= e(implode(', ', $ideia['materiais'])) ?></p>
                        <?php if (!empty($ideia['referencias'])): ?>
                            <div class="actions-row idea-actions">
                                <?php foreach ($ideia['referencias'] as $ref): ?>
                                    <a class="button secondary" href="<?= e($ref['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($ref['label']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<?php render_footer(); ?>

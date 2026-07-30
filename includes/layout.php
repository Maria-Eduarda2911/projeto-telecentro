<?php

function render_header(array $site, string $tituloPagina, string $subtitulo = '', bool $mostrarBotaoInicio = true): void
{
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($tituloPagina) ?> | <?= e($site['nome_site']) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <main class="container main-content">
            <section class="page-header">
                <p class="eyebrow">Portal educacional</p>
                <div class="page-header-top">
                    <div>
                        <h1><?= e($tituloPagina) ?></h1>
                        <?php if ($subtitulo !== ''): ?>
                            <p class="lead"><?= e($subtitulo) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($mostrarBotaoInicio): ?>
                        <a class="button secondary header-home-button" href="index.php">⬅ Voltar para o início</a>
                    <?php endif; ?>
                </div>
            </section>
    <?php
}

function render_footer(): void
{
    ?>
        </main>
        <footer class="footer">
            <div class="container footer-inner">
                <p class="footer-credit">
                    Projeto desenvolvido por
                    <a href="https://github.com/Maria-Eduarda2911" target="_blank" rel="noopener noreferrer">Maria Eduarda</a>
                </p>
                <p class="footer-context">Projeto criado com carinho para ser utilizado pela equipe do Telecentro da Secretaria de Educação de Olinda.</p>
            </div>
        </footer>
    </body>
    </html>
    <?php
}

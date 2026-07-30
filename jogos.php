<?php

$site = require __DIR__ . '/includes/conteudo.php';
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/layout.php';

$jogos = [
    [
        'nome' => 'Escola Games',
        'descricao' => 'Leitura, matemática e raciocínio em jogos simples de navegar.',
        'url' => 'https://www.escolagames.com.br/',
        'imagem' => 'https://static.escolagames.com.br/site/imgs/logo-escolagames.svg',
    ],
    [
        'nome' => 'A Coruja Boo',
        'descricao' => 'Jogos, histórias e atividades para diferentes idades.',
        'url' => 'https://acorujaboo.com/',
        'imagem' => 'https://acorujaboo.com/wp-content/uploads/logo-ACORUJABOO-3.webp',
    ],
    [
        'nome' => 'Coquinhos',
        'descricao' => 'Atividades escolares para aprender brincando.',
        'url' => 'https://www.coquinhos.com/',
        'imagem' => 'https://www.coquinhos.com/wp-content/uploads/2021/07/coquinhos-logo-4.png',
    ],
    [
        'nome' => 'HVirtua',
        'descricao' => 'Alfabetização, matemática e coordenação motora.',
        'url' => 'https://jogoseducativos.hvirtua.com/',
        'imagem' => 'https://hvirtua.com/jogoseducativos/wp-content/uploads/2024/01/Logo120B.png',
    ],
    [
        'nome' => 'Poki',
        'descricao' => 'Jogos gratuitos que abrem direto no navegador.',
        'url' => 'https://poki.com/br',
        'imagem' => 'https://a.poki-cdn.com/img/favicon.svg',
    ],
    [
        'nome' => 'Iguinho',
        'descricao' => 'Jogos, textos e animações para crianças.',
        'url' => 'https://iguinho.com.br',
        'imagem' => 'https://iguinho.com.br/favicon.png',
    ],
    [
        'nome' => 'Ludo Educativo',
        'descricao' => 'Português, matemática, geografia, história e inglês.',
        'url' => 'https://www.ludoeducativo.com.br/pt/',
        'imagem' => 'https://www.ludoeducativo.com.br/bundles/aptorgamesportal/images/logo.png',
    ],
    [
        'nome' => 'Nova Escola — Jogos',
        'descricao' => 'Ideias e jogos pensados para quem está em sala de aula.',
        'url' => 'https://novaescola.org.br/guias/1427/jogos',
        'imagem' => 'https://novaescola.org.br/imagens/favicon.ico',
    ],
    [
        'nome' => 'Nosso Clubinho',
        'descricao' => 'Jogos, histórias, livros e atividades variadas.',
        'url' => 'https://www.nossoclubinho.com.br',
        'imagem' => 'https://nossoclubinho.com.br/wp-content/uploads/2012/09/logo.png',
    ],
    [
        'nome' => 'Racha Cuca',
        'descricao' => 'Desafios de lógica, atenção e raciocínio.',
        'url' => 'https://rachacuca.com.br/',
        'imagem' => 'https://rachacuca.com.br/static/images/rachacuca-logo-menu.png',
    ],
    [
        'nome' => 'Wordwall',
        'descricao' => 'Quizzes e jogos criados por professores.',
        'url' => 'https://wordwall.net/pt-br/community/jogos-educativos',
        'imagem' => 'https://app.cdn.wordwall.net/static/content/images/favicon.2evmh0qrg1aqcgft8tddfea2.ico',
    ],
];

render_header($site, 'Atividades Plugadas', 'Plataformas e jogos para usar no Telecentro. Todos os links abrem em nova aba.', false);
?>

<section class="notice-card">
    <h2>Atividades Plugadas</h2>
    <p>Use esta página para acessar desafios de matemática, português e inglês, e também plataformas de tecnologia e robótica educacional.</p>
    <a class="button secondary back-home-button" href="index.php">⬅ Voltar para o início</a>
</section>

<section class="games-grid">
    <?php foreach ($jogos as $jogo): ?>
        <article class="game-card">
            <div class="game-image game-logo">
                <img src="<?= e($jogo['imagem']) ?>" alt="<?= e($jogo['nome']) ?>" loading="lazy">
            </div>
            <h3><?= e($jogo['nome']) ?></h3>
            <p><?= e($jogo['descricao']) ?></p>
            <a class="button primary" href="<?= e($jogo['url']) ?>" target="_blank" rel="noopener noreferrer">Abrir site</a>
        </article>
    <?php endforeach; ?>
</section>

<section class="notice-card">
    <h2>Plataformas de Tecnologia e Robótica</h2>
    <p>Este bloco reúne recursos oficiais para introduzir programação, eletrônica e construção criativa.</p>
</section>

<section class="portals-grid">
    <article class="game-card">
        <h3>LEGO Education - Aprender com construções</h3>
        <p>Conecte blocos e atividades com propostas pedagógicas para sala de aula.</p>
        <a class="button primary" href="https://education.lego.com/pt-br" target="_blank" rel="noopener noreferrer">Abrir LEGO Education</a>
    </article>
    <article class="game-card">
        <h3>Arduino Education - Eletrônica e Programação</h3>
        <p>Plataformas e cursos para trabalhar circuitos, codificação e projetos maker.</p>
        <a class="button primary" href="https://www.arduino.cc/education" target="_blank" rel="noopener noreferrer">Abrir Arduino Education</a>
    </article>
    <article class="game-card">
        <h3>Makey Makey - Jogos interativos com circuitos</h3>
        <p>Use objetos do cotidiano para criar controles e brincar com eletrônica.</p>
        <a class="button primary" href="https://makeymakey.com/pages/plug-and-play-makey-makey-apps" target="_blank" rel="noopener noreferrer">Abrir Makey Makey</a>
    </article>
    <article class="game-card">
        <h3>Code Jr Kids - Lógica de programação</h3>
        <p>Atividades simples para aprender sequências, loops e comandos divertidos.</p>
        <a class="button primary" href="https://moshe1ch-kidi.github.io/codejrkids/" target="_blank" rel="noopener noreferrer">Abrir Code Jr Kids</a>
    </article>
    <article class="game-card">
        <h3>SPIKE App - Programação de Robôs LEGO</h3>
        <p>Explore programação de robôs com projetos da linha SPIKE Prime.</p>
        <a class="button primary" href="https://spike.legoeducation.com/" target="_blank" rel="noopener noreferrer">Abrir SPIKE App</a>
    </article>
</section>

<?php render_footer(); ?>

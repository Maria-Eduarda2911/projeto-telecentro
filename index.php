<?php
// ================================================================
// 1. INICIALIZAÇÃO E SESSÃO DO ALUNO
// ================================================================
session_start();

// Se clicar em "Trocar de Aluno", limpa a memória e recarrega a página
if (isset($_GET['trocar_aluno'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Se o formulário de identificação for enviado, salva na sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_aluno'])) {
    $_SESSION['brink_nome']   = trim($_POST['nome_aluno']);
    $_SESSION['brink_escola'] = trim($_POST['escola_aluno'] ?? 'Não informada');
    
    // Redireciona para a mesma página para limpar o POST e evitar reenvio
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ================================================================
// 2. INCLUDES DO SISTEMA
// ================================================================
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

<div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1.5rem; margin-bottom: 2rem;">
    
    <section class="notice-card" style="flex: 1; min-width: 300px; margin: 0;">
        <?php if (!empty($_SESSION['brink_nome'])): ?>
            
            <p class="eyebrow">Jogador Atual</p>
            <h2>Olá, <?= htmlspecialchars($_SESSION['brink_nome']) ?>!</h2>
            <p>Escola: <strong><?= htmlspecialchars($_SESSION['brink_escola']) ?></strong></p>
            <div class="actions-row">
    <a href="?trocar_aluno=1" class="button secondary">Trocar de Aluno</a>
    <button type="button" onclick="abrirModal()" class="button secondary">Ver Quadro de Honra</button>
</div>

        <?php else: ?>
            
            <p class="eyebrow">Antes de começar</p>
            <h2>Quem está jogando?</h2>
            <p>Preencha seus dados para entrar no Quadro de Honra!</p>
            
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px; margin-top: 1rem;">
                <input type="text" name="nome_aluno" placeholder="Qual o seu nome?" required 
                       style="padding: 12px 16px; border: 2px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; color: var(--text); outline: none; transition: border-color var(--transition);"
                       onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                       
                <input type="text" name="escola_aluno" placeholder="Nome da sua escola" required 
                       style="padding: 12px 16px; border: 2px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; color: var(--text); outline: none; transition: border-color var(--transition);"
                       onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                       
                <button type="submit" class="button primary" style="width: 100%;">Salvar e Escolher Matéria</button>
            </form>

        <?php endif; ?>
    </section>

    <section class="notice-card" style="flex: 1; min-width: 300px; margin: 0;">
        <p class="eyebrow">Para o Telecentro de Olinda</p>
        <h2>Feito para quem ensina e para quem aprende</h2>
        <p>Um espaço simples, acolhedor e fácil de navegar — pensado junto com a realidade de quem trabalha com crianças de 5 a 12 anos.</p>
    </section>

</div>

<section class="year-grid">
    <?php foreach (range(1, 5) as $ano): ?>
        
        <details class="subject-card subject-accordion" style="margin-bottom: 16px;">
            <summary>
                <div class="subject-head">
                    <div class="subject-icon" aria-hidden="true">📘</div>
                    <div>
                        <h2><?= e($ano) ?>º ano</h2>
                        <p>Questões de Matemática, Português e Inglês com 5 perguntas.</p>
                        <span class="accordion-hint">Escolha a matéria e a quantidade</span>
                    </div>
                </div>
            </summary>

            <div class="subjects-list" style="margin-top: 1rem; padding-top: 0.5rem;">
                <?php foreach ($site['materias'] as $slug => $materia): ?>
                    
                    <div class="subject-row">
                        <div>
                            <h3><?= e($materia['titulo']) ?></h3>
                            <p><?= e($materia['descricao']) ?></p>
                        </div>
                        <div class="level-grid" style="min-width: 200px;">
                            <a class="button primary" style="padding: 0;" href="<?= e(desafio_url($slug, 5, $ano)) ?>">5</a>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </details>

    <?php endforeach; ?>
</section>
<div id="modalRanking" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--surface); padding:24px; border-radius:var(--radius); width:90%; max-width:600px; max-height:80vh; overflow-y:auto; border: 6px solid var(--primary); box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        <h2 style="color:var(--primary); text-align:center;">🏆 Quadro de Honra</h2>
        <div id="modalConteudo" style="margin-top:20px;">
            <p style="text-align:center;">Carregando ranking...</p>
        </div>
        <button onclick="fecharModal()" class="button primary" style="width:100%; margin-top:20px;">Fechar</button>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById('modalRanking').style.display = 'flex';
    // Carrega o ranking via fetch (simulando uma chamada)
    // Aqui assumimos que você pode criar um arquivo chamado 'ranking_json.php' 
    // ou apenas exibir o conteúdo que já está no banco.
    fetch('ranking_json.php')
        .then(response => response.text())
        .then(data => { document.getElementById('modalConteudo').innerHTML = data; });
}
function fecharModal() {
    document.getElementById('modalRanking').style.display = 'none';
}
</script>
<?php render_footer(); ?>
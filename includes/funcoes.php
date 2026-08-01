<?php

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function desafio_url(string $materia, int $quantidade, int $ano = 1): string
{
    return 'desafio.php?materia=' . urlencode($materia) . '&q=' . $quantidade . '&ano=' . $ano;
}

function ano_valido(int $ano): int
{
    return ($ano >= 1 && $ano <= 5) ? $ano : 1;
}


function classificar_desempenho(int $acertos, int $total): string
{
    if ($total === 0) {
        return 'Vamos começar.';
    }

    $percentual = ($acertos / $total) * 100;

    if ($percentual >= 90) {
        return 'Excelente desempenho.';
    }

    if ($percentual >= 70) {
        return 'Muito bom.';
    }

    if ($percentual >= 50) {
        return 'Bom trabalho.';
    }

    return 'Continue praticando.';
}

function quantidade_valida(int $quantidade): int
{
    $permitidas = [5, 10, 15, 20];
    return in_array($quantidade, $permitidas, true) ? $quantidade : 5;
}

function percentual(int $acertos, int $total): int
{
    if ($total === 0) {
        return 0;
    }
    return (int) round(($acertos / $total) * 100);
}

function emoji_desempenho(int $acertos, int $total): string
{
    $p = percentual($acertos, $total);
    if ($p >= 90) return '🏆';
    if ($p >= 70) return '🎉';
    if ($p >= 50) return '👍';
    if ($p >= 30) return '💪';
    return '🌈';
}

function mensagem_motivacional(int $acertos, int $total): string
{
    $p = percentual($acertos, $total);
    if ($total === 0) {
        return 'Vamos começar?';
    }
    if ($p === 100) {
        return 'Perfeito! Você gabaritou. Que aluno(a) incrível! 🎊';
    }
    if ($p >= 90) {
        return 'Excelente! Você está quase lá, continue assim! ⭐';
    }
    if ($p >= 70) {
        return 'Muito bom! Com um pouco mais de prática você chega lá! 🚀';
    }
    if ($p >= 50) {
        return 'Bom trabalho! Revise as respostas e tente mais uma vez! 📖';
    }
    if ($p >= 30) {
        return 'Continue praticando! Cada erro é uma chance de aprender. 🌱';
    }
    return 'Não desista! O importante é tentar e aprender com cada pergunta. 💖';
}

function busca_google_imagens_url(string $termo): string
{
    return 'https://www.google.com/search?tbm=isch&q=' . rawurlencode($termo);
}

function busca_pinterest_url(string $termo): string
{
    return 'https://www.pinterest.com/search/pins/?q=' . rawurlencode($termo);
}

function imagem_da_pergunta(string $materiaSlug, array $questao, int $indice): string
{
    if (!empty($questao['imagem'])) {
        return $questao['imagem'];
    }

    $texto = mb_strtolower(($questao['pergunta'] ?? '') . ' ' . ($questao['explicacao'] ?? ''), 'UTF-8');

    if ($materiaSlug === 'matematica') {
        if (str_contains($texto, 'quadrado') || str_contains($texto, 'lados') || str_contains($texto, 'triâng') || str_contains($texto, 'triangulo') || str_contains($texto, 'forma')) {
            return 'assets/img/quiz/math-shapes.svg';
        }

        if (str_contains($texto, '+') || str_contains($texto, '-') || str_contains($texto, 'conta') || str_contains($texto, 'somar') || str_contains($texto, 'subtr') || str_contains($texto, 'resultado')) {
            return 'assets/img/quiz/math-plus.svg';
        }

        return 'assets/img/quiz/math-count.svg';
    }

    if ($materiaSlug === 'portugues') {
        if (str_contains($texto, 'letra') || str_contains($texto, 'alfabet') || str_contains($texto, 'sílab') || str_contains($texto, 'silab') || str_contains($texto, 'rima') || str_contains($texto, 'palavra')) {
            return 'assets/img/quiz/portuguese-letters.svg';
        }

        if (str_contains($texto, 'frase') || str_contains($texto, 'pergunta') || str_contains($texto, 'plural') || str_contains($texto, 'acento') || str_contains($texto, 'verbo') || str_contains($texto, 'substantivo') || str_contains($texto, 'adjetivo') || str_contains($texto, 'complete')) {
            return 'assets/img/quiz/portuguese-speech.svg';
        }

        return 'assets/img/quiz/portuguese-books.svg';
    }

    if ($materiaSlug === 'ingles') {
        if (str_contains($texto, 'color') || str_contains($texto, 'blue') || str_contains($texto, 'red') || str_contains($texto, 'green') || str_contains($texto, 'yellow') || str_contains($texto, 'black') || str_contains($texto, 'brown')) {
            return 'assets/img/quiz/english-words.svg';
        }

        if (str_contains($texto, 'morning') || str_contains($texto, 'night') || str_contains($texto, 'hello') || str_contains($texto, 'greet') || str_contains($texto, 'say') || str_contains($texto, 'word')) {
            return 'assets/img/quiz/english-world.svg';
        }

        return 'assets/img/quiz/english-words.svg';
    }

    return 'assets/img/quiz/generic-kids.svg';
}

<?php

$desafios = require __DIR__ . '/desafios_20.php';

return [
    'nome_site' => 'Brinkeduca',
    'descricao_site' => 'Jogos e desafios educativos da Prefeitura de Olinda para crianças de 5 a 12 anos.',
    'materias' => [
        'matematica' => [
            'titulo' => 'Matemática',
            'icone' => '🔢',
            'descricao' => 'Contas simples, comparação e raciocínio lógico.',
            'anos' => $desafios['matematica'],
        ],
        'portugues' => [
            'titulo' => 'Português',
            'icone' => '📘',
            'descricao' => 'Leitura, letras, palavras, rimas e frases curtas.',
            'anos' => $desafios['portugues'],
        ],
        'ingles' => [
            'titulo' => 'Inglês',
            'icone' => '🌎',
            'descricao' => 'Palavras básicas, vocabulário e compreensão simples.',
            'anos' => $desafios['ingles'],
        ],
    ],
];

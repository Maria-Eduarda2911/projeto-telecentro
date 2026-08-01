-- ================================================================
-- BRINKEDUCA — SCHEMA MySQL Atualizado
-- ================================================================

DROP TABLE IF EXISTS rankings;
DROP TABLE IF EXISTS questoes;

-- ================================================================
-- TABELA: questoes
-- ================================================================
CREATE TABLE questoes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    materia     VARCHAR(20)  NOT NULL COMMENT 'matematica | portugues | ingles',
    ano         TINYINT      NOT NULL COMMENT '1 a 5 (ano escolar)',
    pergunta    VARCHAR(500) NOT NULL COMMENT 'O enunciado da pergunta',
    opcao_a     VARCHAR(200) NOT NULL COMMENT 'Primeira alternativa',
    opcao_b     VARCHAR(200) NOT NULL COMMENT 'Segunda alternativa',
    opcao_c     VARCHAR(200) NOT NULL COMMENT 'Terceira alternativa',
    resposta    VARCHAR(200) NOT NULL COMMENT 'O TEXTO da alternativa correta (exatamente igual a opcao_X)',
    explicacao  VARCHAR(500) NOT NULL COMMENT 'Explicação que aparece na revisão',
    emoji       VARCHAR(20)  DEFAULT '❓' COMMENT 'Emoji ao lado da pergunta',
    criado_em   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_materia_ano (materia, ano)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- TABELA: rankings
-- ================================================================
CREATE TABLE rankings (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(80)  NOT NULL COMMENT 'Nome da criança',
    escola     VARCHAR(100) DEFAULT NULL COMMENT 'Nome da escola da criança',
    pontuacao  TINYINT      NOT NULL COMMENT 'Quantidade de acertos (ex: 4 de 5)',
    total      TINYINT      NOT NULL DEFAULT 5 COMMENT 'Total de perguntas (sempre 5)',
    materia    VARCHAR(20)  NOT NULL COMMENT 'matematica | portugues | ingles',
    ano        TINYINT      NOT NULL COMMENT '1 a 5',
    data       DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ranking_pontuacao (pontuacao DESC, data DESC),
    INDEX idx_materia_ano_ranking (materia, ano, pontuacao DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
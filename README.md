# Brinkeduca

Portal educacional em PHP puro, desenvolvido para o **Telecentro da Secretaria de Educação de Olinda**. O projeto oferece desafios interativos, uma vitrine de jogos externos e atividades desplugadas para crianças de 5 a 12 anos — com foco em simplicidade, acessibilidade e zero distrações.

---

## Contexto e impacto social

O Telecentro de Olinda atende crianças e adolescentes em um ambiente onde o acesso à tecnologia precisa ser produtivo, seguro e pedagógico. Muitas plataformas educacionais disponíveis na internet carregam anúncios, rastreadores e interfaces confusas — barreiras reais para quem trabalha com turmas heterogêneas e tempo limitado.

O Brinkeduca nasceu para preencher esse espaço: um portal leve, sem cadastro, sem banco de dados e sem dependências externas, que o educador pode abrir no navegador e usar imediatamente. Cada decisão de arquitetura e design foi tomada pensando em quem está do outro lado da tela — criança aprendendo e adulto mediando.

---

## O que o projeto entrega

| Área | Descrição |
|------|-----------|
| **Desafios educativos** | Quizzes de Matemática, Português e Inglês com 5, 10, 15 ou 20 perguntas, feedback imediato e ilustrações SVG |
| **Jogos externos** | Vitrine curada de 11 portais educacionais com links diretos |
| **Atividades desplugadas** | Propostas sem tela para Matemática, Português e Informática; hubs de Leitura e Pintura com portais externos |
| **Navegação clara** | Botão "Voltar para o início" padronizado em todas as subpáginas |

---

## Decisões de arquitetura

### PHP modular, sem framework

O projeto usa PHP puro com separação clara de responsabilidades:

- **Páginas** (`index.php`, `desafio.php`, `jogos.php`, `desplugadas.php`) — roteamento simples via query strings, sem `.htaccess` ou router
- **Dados** (`includes/conteudo.php`, `desafios_20.php`, `desplugadas.php`) — arrays PHP como banco de conteúdo, fácil de editar por educadores
- **Apresentação** (`includes/layout.php`) — cabeçalho e rodapé compartilhados via funções `render_header()` e `render_footer()`
- **Utilitários** (`includes/funcoes.php`) — escape HTML, URLs, classificação de desempenho, seleção de imagens

Essa escolha elimina dependências de Composer, facilita deploy em qualquer servidor com PHP 8+ e permite que o conteúdo seja atualizado editando arquivos `.php` diretamente.

### Acessibilidade e baixo ruído visual

- HTML semântico com `<details>`/`<summary>` para acordeões nativos (sem JavaScript)
- Contraste adequado, tipografia legível (Segoe UI / Inter / Arial) e cantos arredondados suaves
- Design System inspirado no painel WordPress: fundo cinza confortável, cards brancos, bordas sutis
- Emoji nativo nas perguntas do quiz, com suporte a chave `emoji` no array da questão
- Links externos com `rel="noopener noreferrer"` e `target="_blank"`

### Privacidade

- Sem cookies, sem analytics, sem rastreadores, sem anúncios
- Nenhum dado pessoal é coletado ou armazenado
- Formulários processados via POST na mesma página, sem persistência

---

## Estrutura do projeto

```
projeto-telecentro/
├── index.php                  # Página inicial — matérias e seleção de dificuldade
├── desafio.php                # Quiz com seletor de dificuldade e feedback
├── jogos.php                    # Vitrine de sites de jogos educativos
├── desplugadas.php              # Atividades sem tela e hubs de Leitura/Pintura
├── includes/
│   ├── layout.php               # Cabeçalho, rodapé e botão de retorno
│   ├── funcoes.php              # Funções auxiliares
│   ├── conteudo.php               # Configuração do site e matérias
│   ├── desafios_20.php            # Banco de 60 perguntas (20 por matéria)
│   └── desplugadas.php            # Banco de atividades e portais externos
└── assets/
    ├── css/style.css              # Design System completo
    └── img/quiz/                  # Ilustrações SVG para o quiz
```

---

## Como rodar localmente

Requisito: PHP 8.0 ou superior.

```bash
cd projeto-telecentro
php -S localhost:8000
```

Abra [http://localhost:8000](http://localhost:8000) no navegador.

Para deploy em produção, basta copiar os arquivos para um diretório servido por Apache ou Nginx com suporte a PHP.

---

## Desenvolvedora

**Maria Eduarda** — [github.com/Maria-Eduarda2911](https://github.com/Maria-Eduarda2911)

Projeto criado com carinho para ser utilizado no Telecentro da Secretaria de Educação de Olinda.

---

## Licença

Projeto educacional de uso livre no contexto do Telecentro de Olinda.

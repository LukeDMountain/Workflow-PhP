feat: adiciona painel Fluxo para organização de tarefas

Implementa uma interface responsiva em português para organizar tarefas
do trabalho, com visualização do progresso e filtros por categoria.

Funcionalidades:
- Criar tarefas com título, descrição, prazo e prioridade.
- Editar os dados de uma tarefa existente.
- Marcar tarefas como concluídas ou pendentes.
- Remover tarefas.
- Filtrar tarefas entre todas, para hoje, próximas e concluídas.
- Exibir contadores e progresso com base nas tarefas da sessão.
- Apresentar mensagens de confirmação, validação e estado vazio.

Implementação:
- Adiciona rotas Laravel para listar, criar, atualizar e remover tarefas.
- Centraliza validação e operações da sessão no TaskController.
- Armazena as tarefas na sessão Laravel, sem criar tabelas ou modelos.
- Cria a tela Blade do painel e seus estilos responsivos.
- Remove a dependência de fontes externas para permitir compilar os assets
  sem baixar fontes durante o build.

Como iniciar localmente:
1. Instale as dependências PHP:
   composer install

2. Crie o arquivo de ambiente, caso ainda não exista:
   cp .env.example .env

3. Gere a chave da aplicação:
   php artisan key:generate

4. No arquivo .env, configure a sessão para usar arquivos:
   SESSION_DRIVER=file

5. Instale as dependências do frontend:
   npm install

6. Inicie o servidor Laravel em um terminal:
   php artisan serve

7. Inicie o Vite em outro terminal:
   npm run dev

8. Acesse o link que o terminal devolver no navegador.

As tarefas ficam disponíveis enquanto a sessão estiver ativa. Nenhuma conexão
com banco de dados é necessária para armazená-las neste primeiro commit.

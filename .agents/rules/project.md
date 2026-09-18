# HarusDesk - Project Rules

## Projeto

Este é um projeto Laravel 11 desenvolvido em PHP 8.4.

Stack principal:
- Laravel 11
- PHP 8.4
- Laravel Sanctum
- MySQL
- Redis
- Docker
- PHPUnit/Pest
- Laravel Pint

---

## Arquitetura

O projeto busca manter responsabilidades bem separadas.

Estrutura preferencial:

Controller
    ↓
Action
    ↓
Repository
    ↓
Model

Componentes auxiliares quando fizerem sentido:

Request → validação da entrada
DTO → transporte/representação de dados
Resource → transformação da resposta
Policy → autorização

Não aplicar esses padrões apenas por obrigação. Cada abstração deve ter um motivo concreto.

---

## Controllers

Controllers devem ser finos.

Responsabilidades preferenciais:

1. Receber a requisição.
2. Utilizar o Form Request para validação.
3. Obter os dados necessários.
4. Chamar a Action responsável pela operação.
5. Retornar a resposta apropriada.

Evitar colocar regras de negócio complexas dentro dos Controllers.

---

## Actions

Actions devem concentrar regras de negócio relacionadas à operação.

As Actions não devem acessar diretamente `auth()`.

Quando uma informação do usuário autenticado for necessária, recebê-la explicitamente:

```php
$action->execute($user, $data);
```

em vez de depender diretamente de:

```php
auth()->user();
```

O objetivo é manter as dependências explícitas e facilitar testes.

---

## DTOs

Utilizar DTOs quando ajudarem a representar claramente os dados de uma operação.

Não criar DTOs apenas por formalidade.

Evitar DTOs que apenas repitam estruturas existentes sem trazer benefício.

---

## Repositories

Repositories devem encapsular consultas e operações de persistência quando isso trouxer benefício real para organização, reutilização ou isolamento da infraestrutura.

Não criar Repository para toda consulta simples automaticamente.

Evitar Repositories que apenas reproduzam mecanicamente métodos do Eloquent sem acrescentar valor.

---

## Multi-tenancy

O sistema utiliza `enterprise_id` para separar dados entre empresas.

Novas funcionalidades devem preservar o isolamento entre empresas.

Modelos pertencentes a uma empresa devem respeitar as regras de escopo por `enterprise_id` existentes no projeto.

O acesso de administradores e usuários comuns pode possuir regras diferentes.

Não contornar os mecanismos existentes de isolamento de tenant sem uma justificativa explícita.

---

## Autorização

Utilizar Policies/Gates para regras de autorização.

Evitar espalhar verificações de permissão pelos Controllers, Actions e outros componentes quando uma Policy puder centralizar a regra.

Não duplicar regras de autorização sem necessidade.

---

## Código

Priorizar:

- legibilidade;
- simplicidade;
- responsabilidades bem definidas;
- baixo acoplamento;
- código idiomático de Laravel;
- testes;
- manutenção fácil.

Evitar:

- overengineering;
- abstrações prematuras;
- interfaces sem necessidade;
- padrões criados apenas para "seguir arquitetura";
- código excessivamente genérico;
- refatorações não relacionadas à tarefa.

A solução mais simples que atende corretamente ao problema deve ser preferida.

---

## Desenvolvimento com IA

O desenvolvedor está utilizando IA como ferramenta de aprendizado.

Para tarefas não triviais, antes de modificar arquivos:

1. Analise o problema.
2. Explique brevemente a estratégia de solução.
3. Identifique os arquivos que provavelmente serão envolvidos.
4. Explique as decisões arquiteturais relevantes.
5. Quando apropriado, permita que o desenvolvedor tente implementar a solução primeiro.
6. Só implemente diretamente quando isso for solicitado ou quando a tarefa for claramente mecânica/repetitiva.

Durante debugging, priorizar inicialmente:

- perguntas;
- pistas;
- explicações conceituais;

em vez de entregar imediatamente a solução completa.

Se o desenvolvedor demonstrar que quer aprender o conceito, não pule diretamente para uma implementação pronta.

Depois de realizar alterações, explique:

- o que foi alterado;
- por que foi alterado;
- quais conceitos de PHP/Laravel estão envolvidos;
- quais riscos ou efeitos colaterais existem;
- quais testes devem ser executados.

---

## Nível de assistência

Use esta ordem de assistência quando a tarefa tiver valor de aprendizado:

1. Pergunta orientadora
2. Pista
3. Explicação do conceito
4. Exemplo parcial
5. Implementação completa

Não avançar para uma solução completa sem necessidade.

Para tarefas mecânicas, repetitivas ou de baixo valor educacional, a IA pode implementar diretamente.

---

## Análise antes de alterar

Antes de modificar código:

- analisar a implementação existente;
- procurar funcionalidades semelhantes;
- identificar padrões já utilizados no projeto;
- verificar dependências entre os arquivos envolvidos;
- evitar assumir que uma arquitetura deve ser criada do zero.

Não reestruturar partes do projeto sem necessidade.

Não modificar arquivos que não tenham relação com a tarefa, salvo quando houver uma justificativa clara.

---

## Dependências

Não adicionar novas dependências sem explicar:

1. qual problema ela resolve;
2. por que as ferramentas existentes não são suficientes;
3. qual impacto ela terá no projeto.

Preferir recursos nativos do Laravel/PHP quando forem suficientes.

---

## Testes

Quando uma alteração modificar comportamento da aplicação, considerar a criação ou atualização de testes.

Executar os testes relevantes antes de considerar a tarefa concluída, quando possível.

Comandos principais:

```bash
php artisan test
```

e, quando aplicável:

```bash
vendor/bin/pint
```

Não afirmar que uma alteração foi validada se os testes relevantes não tiverem sido executados.

---

## Banco de dados

Antes de alterar estrutura ou consultas do banco:

- verificar os Models existentes;
- verificar migrations relacionadas;
- verificar relacionamentos;
- verificar scopes;
- considerar o impacto sobre multi-tenancy.

Evitar alterações destrutivas sem confirmação explícita.

---

## API

Para novos endpoints, seguir os padrões já existentes no projeto.

Quando aplicável, considerar:

- Form Request;
- Action;
- DTO;
- Repository;
- Policy;
- Resource;
- testes.

Não criar todos esses componentes automaticamente. Utilizar somente os necessários para a operação.

---

## Segurança

Não remover ou contornar autenticação, autorização, validação ou isolamento de tenant para facilitar uma implementação.

Dados recebidos do usuário devem ser tratados como não confiáveis.

Evitar exposição desnecessária de dados internos nas respostas da API.

---

## Comunicação

Ao trabalhar em uma tarefa:

1. Informe o plano de forma breve.
2. Para tarefas de aprendizado, avance por etapas.
3. Explique decisões importantes.
4. Ao modificar arquivos, informe o que mudou.
5. Informe os testes executados.
6. Informe qualquer incerteza ou decisão que precise ser tomada pelo desenvolvedor.

Não gerar explicações excessivamente longas para alterações simples.

---

## Regra principal

O objetivo não é apenas fazer o código funcionar.

O objetivo é produzir código que o desenvolvedor consiga:

- entender;
- explicar;
- testar;
- manter;
- modificar posteriormente.

A IA deve acelerar o desenvolvimento sem substituir o aprendizado do desenvolvedor.

# 🎫 HarusDesk API — Sistema Multi-tenant de Helpdesk

[![PHP Version](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net/)
[![Laravel Framework](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tests](https://img.shields.io/badge/Tests-78%20Passed%20(100%25)-success?logo=phpunit&logoColor=white)](https://phpunit.de/)
[![Code Style](https://img.shields.io/badge/Code%20Style-Laravel%20Pint-brightgreen)](https://laravel.com/docs/pint)

O **HarusDesk** é uma API RESTful moderna, robusta e escalável desenvolvida para plataformas SaaS de Helpdesk / Atendimento ao Cliente. 

O projeto foi arquitetado com foco em **Clean Architecture**, **SOLID**, **Multi-tenancy rigoroso por isolamento de empresa**, **Segurança contra IDOR** e **Cobertura abrangente de testes automatizados**.

---

## 🏛️ Destaques de Arquitetura & Engenharia

```
[ HTTP Request (JSON) ]
          ↓
[ Form Request + Custom Rules ] ➔ (Validação, Fail-fast e Tenant Scoping)
          ↓
[ DTO (Data Transfer Object) ]   ➔ (Tipagem imutável com PHP 8.4 readonly)
          ↓
[ Action (Single Responsibility) ] ➔ (Regras de Negócio e Orquestração)
          ↓
[ Repository Pattern ]            ➔ (Abstração de persistência e consultas)
          ↓
[ Eloquent Model + Global Scope ] ➔ (Multi-tenancy automático & Soft Deletes)
```

- **Clean Architecture & SOLID:** Separação estrita de responsabilidades. Controllers magros que apenas recebem a requisição, delegam para Actions de responsabilidade única e retornam Resources formatados.
- **Isolamento Multi-tenant Rigoroso:**
  - `BelongsToEnterprise` Global Scope no Eloquent para isolamento transparente de consultas.
  - Custom Validation Rules e Macros (`Rule::existsForTenant`, `SectorAcceptsTickets`, `ValidAttendant`) para prevenir falhas de referência direta insegura a objetos (*IDOR*).
- **Autorização Granular (RBAC + Policies):**
  - Perfis de acesso bem definidos: `Super Admin`, `Admin da Empresa`, `Atendente` e `Solicitante (Requester)`.
  - Policies dedicadas por módulo (`TicketPolicy`, `SectorPolicy`, `UserPolicy`).
- **Segurança de Dados:** Suporte a **Soft Deletes** para rastreabilidade e integridade histórica de chamados e usuários.
- **Suíte de Testes Automatizados:** Cobertura de testes de Feature para cenários felizes (*happy paths*) e fluxos de erro/violações de permissão (*sad paths*).

---

## 🛠️ Stack Tecnológica

- **Linguagem:** PHP 8.4
- **Framework:** Laravel 11
- **Autenticação:** Laravel Sanctum (Tokens Bearer)
- **Banco de Dados:** MySQL 8.0
- **Cache & Filas:** Redis
- **Ambiente & Containerização:** Docker & Docker Compose
- **Testes Automatizados:** PHPUnit & Pest
- **Padronização de Código:** Laravel Pint (PSR-12)

---

## 🚀 Como Executar o Projeto com Docker

### 1. Pré-requisitos
- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/) instalados.
- [Git](https://git-scm.com/)

### 2. Clonar o Repositório
```bash
git clone https://github.com/kakabraga/harusdesk.git
cd harusdesk
```

### 3. Configurar Variáveis de Ambiente
Copie o arquivo de exemplo para o ambiente local:
```bash
cp .env.example .env
```

### 4. Subir os Containers Docker
```bash
docker compose up -d --build
```

### 5. Instalar Dependências e Gerar Chave da Aplicação
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 6. Executar as Migrations e Popular o Banco com Seeders
O seeder gera automaticamente **100 registros realistas por tabela** (Planos, Empresas, Setores, Usuários, Chamados, Interações e Notificações):
```bash
docker compose exec app php artisan migrate:fresh --seed
```

A API estará acessível em: `http://localhost:8000`

---

## 🧪 Executando os Testes Automatizados

Para rodar toda a suíte de testes de Feature e Unitários:
```bash
docker compose exec app php artisan test
```

Para verificar e formatar o estilo de código:
```bash
docker compose exec app vendor/bin/pint
```

---

## 🔑 Usuários Pré-configurados para Testes (Postman)

Todos os usuários criados pelos seeders possuem a senha padrão: `password`

| Perfil | E-mail | Descrição / Permissões |
| :--- | :--- | :--- |
| **Super Admin** | `superadmin@harsudesk.com` | Acesso global a todas as empresas, planos e setores do SaaS. |
| **Admin da Empresa** | `admin@empresa1.com` | Administrador do tenant (Empresa 1). Gerencia setores, atendentes e chamados da sua organização. |
| **Solicitante** | `user@empresa1.com` | Usuário comum. Cria e acompanha seus próprios chamados. |

---

## 📌 Principais Endpoints da API

### Autenticação (`/api/auth`)
- `POST /api/auth/login` — Autenticação e geração de token Sanctum.
- `POST /api/auth/logout` — Revogação de token ativo.
- `GET /api/auth/me` — Dados do usuário logado.

### Chamados / Tickets (`/api/tickets`)
- `GET /api/tickets` — Listagem paginada com filtros (`status`, `priority`, `sector_id`).
- `POST /api/tickets` — Abertura de novo chamado (com validação de setor ativo e tenant).
- `GET /api/tickets/{id}` — Detalhes do chamado.
- `PUT/PATCH /api/tickets/{id}` — Atualizações completas ou parciais (atribuição de atendente, transição de status).
- `DELETE /api/tickets/{id}` — Exclusão lógica (Soft Delete) autorizada.

### Setores / Sectors (`/api/sectors`)
- `GET /api/sectors` — Lista setores da empresa.
- `POST /api/sectors` — Criação de novo setor (com controle de `accepts_tickets` e `active`).
- `PUT/PATCH /api/sectors/{id}` — Edição do setor.
- `DELETE /api/sectors/{id}` — Remoção de setor.

---

## 👤 Autor

Desenvolvido por **Cauê Braga (kaka)**  
- GitHub: [@kakabraga](https://github.com/kakabraga)  
- LinkedIn: [linkedin.com/in/caue-braga](https://www.linkedin.com)

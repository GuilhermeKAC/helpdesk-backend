# HelpDesk API — Documentação

API REST para gerenciamento de tickets de suporte.

---

## Stack

| Componente | Tecnologia | Versão |
|------------|------------|--------|
| Backend | Laravel | 13.x |
| Banco de Dados | PostgreSQL | 16 |
| PHP | PHP | 8.4 |
| Autenticação | Laravel Sanctum | 4.x |
| Filas | Laravel Horizon | 5.x |
| Permissões | Spatie Permission | 7.x |
| Cache / Sessão | Redis | — |

---

## Autenticação

Todas as rotas (exceto `login` e `register`) exigem token Sanctum no header:

```
Authorization: Bearer {token}
```

Token gerado via `POST /api/v1/login`. Sem expiração por padrão — revogado via `POST /api/v1/logout`.

---

## Roles

| Role | Descrição |
|------|-----------|
| `admin` | Acesso total — usuários, categorias, fila, relatórios |
| `technician` | Ver/responder/atribuir tickets, dashboard |
| `customer` | Criar e acompanhar próprios tickets |

---

## Endpoints (resumo)

| Método | Endpoint | Auth | Role |
|--------|----------|:----:|------|
| POST | `/api/v1/login` | — | — |
| POST | `/api/v1/register` | — | — |
| POST | `/api/v1/logout` | ✅ | any |
| GET | `/api/v1/me` | ✅ | any |
| GET/POST | `/api/v1/tickets` | ✅ | any |
| GET/PUT/DELETE | `/api/v1/tickets/{id}` | ✅ | owner/tech/admin |
| POST | `/api/v1/tickets/{id}/assign` | ✅ | admin/tech |
| POST | `/api/v1/tickets/{id}/status` | ✅ | admin/tech |
| POST | `/api/v1/tickets/{id}/reply` | ✅ | any |
| GET | `/api/v1/tickets/{id}/activities` | ✅ | any |
| GET | `/api/v1/dashboard/stats` | ✅ | any |
| GET | `/api/v1/dashboard/charts` | ✅ | any |
| * | `/api/v1/admin/*` | ✅ | admin |

---

## Banco de Dados

```
users
 └── tickets (user_id, technician_id)
      ├── ticket_replies (ticket_id)
      │    └── attachments [morph]
      ├── attachments [morph]
      └── ticket_activities (ticket_id)

categories
 └── tickets (category_id)
```

Schema completo: [`../schema.dbml`](../schema.dbml)  
Contrato da API: [`openapi.yaml`](./openapi.yaml)

---

## Estrutura da Documentação

```
docs/
├── README.md
├── openapi.yaml
├── enums/
│   ├── TicketStatus.md
│   ├── TicketPriority.md
│   └── UserRole.md
├── migrations/
│   ├── users.md
│   ├── categories.md
│   ├── tickets.md
│   ├── ticket_replies.md
│   ├── attachments.md
│   └── ticket_activities.md
├── models/
│   ├── User.md
│   ├── Category.md
│   ├── Ticket.md
│   ├── TicketReply.md
│   ├── Attachment.md
│   └── TicketActivity.md
├── seeders/
│   ├── UserSeeder.md
│   └── CategorySeeder.md
├── controllers/        ← a criar conforme fluxos prontos
├── requests/           ← a criar conforme fluxos prontos
└── services/           ← a criar conforme fluxos prontos
```

---

## Setup local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Credenciais padrão após seed:

| Email | Senha | Role |
|-------|-------|------|
| admin@helpdesk.com | password | admin |

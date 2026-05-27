# Migration: ticket_activities

**Arquivo:** `database/migrations/2026_05_18_181450_create_ticket_activities_table.php`

---

## Tabela: `ticket_activities`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `ticket_id` | bigint | Não | — | FK → tickets |
| `user_id` | bigint | Sim | — | FK → users (null = ação do sistema) |
| `action` | varchar(50) | Não | — | Tipo da ação (ver abaixo) |
| `old_value` | jsonb | Sim | — | Estado anterior |
| `new_value` | jsonb | Sim | — | Novo estado |
| `ip_address` | varchar(45) | Sim | — | IP do autor |
| `user_agent` | text | Sim | — | User agent |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `activities_ticket_created_idx` | `ticket_id`, `created_at` | btree |
| `activities_user_idx` | `user_id` | btree |
| `activities_action_idx` | `action` | btree |
| `activities_created_at_idx` | `created_at` | btree |

## FK

| Coluna | Referencia | On Delete |
|--------|-----------|-----------|
| `ticket_id` | `tickets.id` | CASCADE |
| `user_id` | `users.id` | SET NULL |

## Actions

| Valor | Descrição |
|-------|-----------|
| `created` | Ticket criado |
| `assigned` | Técnico atribuído |
| `status_changed` | Status alterado |
| `replied` | Resposta adicionada |
| `escalated` | Ticket escalado automaticamente |
| `attachment_added` | Arquivo anexado |
| `closed` | Ticket fechado |

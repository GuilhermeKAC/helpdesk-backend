# Migration: tickets

**Arquivo:** `database/migrations/2026_05_18_181447_create_tickets_table.php`

---

## Tabela: `tickets`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `ticket_number` | varchar(20) | Não | — | Código único `HD-YYYY-XXXXXX` (trigger) |
| `user_id` | bigint | Não | — | FK → users (solicitante) |
| `technician_id` | bigint | Sim | — | FK → users (técnico atribuído) |
| `category_id` | bigint | Não | — | FK → categories |
| `title` | varchar(255) | Não | — | Título do ticket |
| `description` | text | Não | — | Descrição completa |
| `status` | varchar(20) | Não | `open` | Ver `TicketStatus` |
| `priority` | varchar(10) | Não | `medium` | Ver `TicketPriority` |
| `assigned_at` | timestamp | Sim | — | Quando foi atribuído |
| `resolved_at` | timestamp | Sim | — | Quando foi resolvido |
| `closed_at` | timestamp | Sim | — | Quando foi fechado |
| `due_date` | timestamp | Sim | — | Prazo SLA |
| `response_time` | integer | Sim | — | Minutos até primeira resposta |
| `resolution_time` | integer | Sim | — | Minutos até resolução |
| `metadata` | jsonb | Sim | — | Campos extras |
| `deleted_at` | timestamp | Sim | — | Soft delete |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `tickets_ticket_number_unique` | `ticket_number` | unique |
| `tickets_user_status_idx` | `user_id`, `status` | btree |
| `tickets_tech_status_idx` | `technician_id`, `status` | btree |
| `tickets_status_priority_idx` | `status`, `priority` | btree |
| `tickets_number_idx` | `ticket_number` | btree |
| `tickets_created_at_idx` | `created_at` | btree |
| `tickets_metadata_gin_idx` | `metadata` | **GIN** |

## FK

| Coluna | Referencia | On Delete |
|--------|-----------|-----------|
| `user_id` | `users.id` | CASCADE |
| `technician_id` | `users.id` | SET NULL |
| `category_id` | `categories.id` | RESTRICT |

## PostgreSQL: trigger `set_ticket_number`

Executa `BEFORE INSERT` e gera automaticamente o `ticket_number` no formato:

```
HD-{ANO}-{SEQUENCE:6}
```

Exemplos: `HD-2026-000001`, `HD-2026-000042`

Dependências criadas pela migration:
- Sequence: `ticket_number_seq`
- Function: `generate_ticket_number()`
- Trigger: `set_ticket_number`

# Migration: ticket_replies

**Arquivo:** `database/migrations/2026_05_18_181448_create_ticket_replies_table.php`

---

## Tabela: `ticket_replies`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `ticket_id` | bigint | Não | — | FK → tickets |
| `user_id` | bigint | Não | — | FK → users (autor) |
| `message` | text | Não | — | Conteúdo da resposta |
| `attachments` | jsonb | Sim | — | Array de IDs de attachments |
| `is_internal` | boolean | Não | `false` | Nota interna (oculta do cliente) |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `replies_ticket_created_idx` | `ticket_id`, `created_at` | btree |
| `replies_user_idx` | `user_id` | btree |
| `replies_internal_idx` | `is_internal` | btree |
| `replies_message_gin_idx` | `to_tsvector('portuguese', message)` | **GIN** |

## FK

| Coluna | Referencia | On Delete |
|--------|-----------|-----------|
| `ticket_id` | `tickets.id` | CASCADE |
| `user_id` | `users.id` | RESTRICT |

## Notas

- `is_internal = true` → visível apenas para admin e technician
- GIN index em `message` permite full-text search em português via `to_tsvector`

# Migration: attachments

**Arquivo:** `database/migrations/2026_05_18_181449_create_attachments_table.php`

---

## Tabela: `attachments`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `attachable_type` | varchar(255) | Não | — | Tipo do modelo pai (morph) |
| `attachable_id` | bigint | Não | — | ID do modelo pai (morph) |
| `user_id` | bigint | Não | — | FK → users (quem fez upload) |
| `filename` | varchar(255) | Não | — | Nome armazenado (uuid) |
| `original_name` | varchar(255) | Não | — | Nome original do upload |
| `mime_type` | varchar(100) | Não | — | Tipo MIME |
| `size` | integer | Não | — | Tamanho em bytes |
| `path` | varchar(500) | Não | — | Caminho no disco |
| `disk` | varchar(20) | Não | `public` | Disco do storage |
| `metadata` | jsonb | Sim | — | Dados extras (dimensões, páginas) |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `attachments_attachable_type_attachable_id_index` | `attachable_type`, `attachable_id` | btree |
| `attachments_user_idx` | `user_id` | btree |
| `attachments_created_at_idx` | `created_at` | btree |

## FK

| Coluna | Referencia | On Delete |
|--------|-----------|-----------|
| `user_id` | `users.id` | RESTRICT |

## Polimorfismo

`attachable_type` assume os valores:

| Valor | Model |
|-------|-------|
| `App\Models\Ticket` | Attachment direto no ticket |
| `App\Models\TicketReply` | Attachment em resposta |

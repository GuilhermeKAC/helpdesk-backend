# Migration: categories

**Arquivo:** `database/migrations/2026_05_18_181446_create_categories_table.php`

---

## Tabela: `categories`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `name` | varchar(255) | Não | — | Nome único da categoria |
| `description` | text | Sim | — | Descrição |
| `color` | char(7) | Não | `#3B82F6` | Cor hex para UI |
| `icon` | varchar(255) | Não | `DocumentIcon` | Ícone Heroicons |
| `auto_assign_technician_id` | bigint | Sim | — | FK → users (técnico padrão) |
| `sla_hours` | integer | Não | `48` | SLA padrão em horas |
| `is_active` | boolean | Não | `true` | Categoria ativa |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `categories_name_unique` | `name` | unique |
| `categories_active_idx` | `is_active` | btree |
| `categories_name_idx` | `name` | btree |

## FK

| Coluna | Referencia | On Delete |
|--------|-----------|-----------|
| `auto_assign_technician_id` | `users.id` | SET NULL |

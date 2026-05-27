# Migration: users

**Arquivos:**
- `database/migrations/0001_01_01_000000_create_users_table.php` — tabela base Laravel
- `database/migrations/2026_05_18_181444_add_fields_to_users_table.php` — campos do domínio

---

## Tabela: `users`

| Coluna | Tipo | Nullable | Padrão | Descrição |
|--------|------|:--------:|--------|-----------|
| `id` | bigserial | Não | auto | PK |
| `name` | varchar(255) | Não | — | Nome completo |
| `email` | varchar(255) | Não | — | E-mail único |
| `email_verified_at` | timestamp | Sim | — | Data de verificação |
| `password` | varchar(255) | Não | — | Senha (hashed) |
| `role` | varchar(255) | Não | `customer` | `admin` \| `technician` \| `customer` |
| `is_active` | boolean | Não | `true` | Usuário ativo |
| `last_login_at` | timestamp | Sim | — | Último login |
| `phone` | varchar(20) | Sim | — | Telefone |
| `preferences` | jsonb | Sim | — | Preferências de UI e notificações |
| `remember_token` | varchar(100) | Sim | — | Token de sessão |
| `deleted_at` | timestamp | Sim | — | Soft delete |
| `created_at` | timestamp | Não | — | — |
| `updated_at` | timestamp | Não | — | — |

## Índices

| Nome | Colunas | Tipo |
|------|---------|------|
| `users_email_unique` | `email` | unique |
| `users_role_active_idx` | `role`, `is_active` | btree |
| `users_created_at_idx` | `created_at` | btree |

# Resource: TicketActivityResource

**Arquivo:** `app/Http/Resources/TicketActivityResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID da atividade |
| `action` | string | Tipo da ação (`created`, `assigned`, `status_changed`, `replied`) |
| `old_value` | string\|null | Valor anterior (ex: status antigo) |
| `new_value` | string\|null | Novo valor (ex: status novo) |
| `user` | UserResource\|null | Usuário que realizou a ação — presente quando `load('user')` |
| `created_at` | string\|null | ISO 8601 |

## Usado em

- `TicketController::activities()` — `GET /api/v1/tickets/{ticket}/activities`

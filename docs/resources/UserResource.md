# Resource: UserResource

**Arquivo:** `app/Http/Resources/UserResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID do usuário |
| `name` | string | Nome completo |
| `email` | string | E-mail |
| `role` | string | Valor do enum (`admin`, `technician`, `customer`) |
| `role_label` | string | Label legível (`Administrador`, `Técnico`, `Cliente`) |
| `is_active` | boolean | Conta ativa |
| `phone` | string\|null | Telefone |
| `created_at` | string\|null | ISO 8601 |

## Usado em

- `AuthController` — `login`, `register`, `me`
- `TicketResource` — campos `user` e `technician` (via `whenLoaded`)
- `TicketReplyResource` — campo `user` (via `whenLoaded`)
- `TicketActivityResource` — campo `user` (via `whenLoaded`)

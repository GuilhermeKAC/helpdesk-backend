# Resource: CategoryResource

**Arquivo:** `app/Http/Resources/CategoryResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID da categoria |
| `name` | string | Nome |
| `description` | string\|null | Descrição |
| `color` | string\|null | Cor hex para UI |
| `icon` | string\|null | Ícone (nome ou classe) |
| `sla_hours` | integer\|null | SLA em horas da categoria |
| `is_active` | boolean | Categoria ativa |

## Usado em

- `TicketResource` — campo `category` (via `whenLoaded`)

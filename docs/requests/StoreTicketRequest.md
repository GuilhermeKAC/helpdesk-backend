# Request: StoreTicketRequest

**Arquivo:** `app/Http/Requests/Api/V1/StoreTicketRequest.php`  
**Usado em:** `POST /api/v1/tickets`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `category_id` | integer | required, exists:categories,id |
| `title` | string | required, max:255 |
| `description` | string | required, max:10000 |
| `priority` | string | required, enum TicketPriority |

## Valores válidos para `priority`

`low` \| `medium` \| `high` \| `urgent`

# Model: TicketReply

**Arquivo:** `app/Models/TicketReply.php`  
**Tabela:** `ticket_replies`

---

## Fillable

`ticket_id`, `user_id`, `message`, `attachments`, `is_internal`

## Casts

| Campo | Cast |
|-------|------|
| `attachments` | `array` |
| `is_internal` | `boolean` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `ticket()` | `belongsTo(Ticket)` | Ticket pai |
| `user()` | `belongsTo(User)` | Autor da resposta |
| `attachments()` | `morphMany(Attachment, attachable)` | Arquivos anexados |

## Scopes

| Scope | Filtro |
|-------|--------|
| `scopePublic($query)` | `is_internal = false` |
| `scopeInternal($query)` | `is_internal = true` |

## Notas

- `is_internal = true` → resposta visível apenas para `admin` e `technician`
- Campo `attachments` (jsonb) armazena array de IDs — os registros reais estão em `attachments` via morph

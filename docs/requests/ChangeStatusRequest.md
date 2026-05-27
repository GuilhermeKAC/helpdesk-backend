# Request: ChangeStatusRequest

**Arquivo:** `app/Http/Requests/Api/V1/ChangeStatusRequest.php`  
**Usado em:** `POST /api/v1/tickets/{ticket}/status`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `status` | string | required, enum TicketStatus |

## Valores válidos para `status`

`open` \| `in_progress` \| `pending` \| `resolved` \| `closed`

## Notas

- Acesso bloqueado para `customer` (403) — verificado no controller
- Não há validação de transição de estados no request — regra de negócio pode ser adicionada no `TicketService`

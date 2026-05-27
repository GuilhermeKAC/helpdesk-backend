# Request: AddReplyRequest

**Arquivo:** `app/Http/Requests/Api/V1/AddReplyRequest.php`  
**Usado em:** `POST /api/v1/tickets/{ticket}/reply`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `message` | string | required, max:10000 |
| `is_internal` | boolean | sometimes |

## Comportamento especial

`prepareForValidation()` força `is_internal = false` quando o usuário autenticado tem role `customer`.  
Isso previne que customers criem notas internas mesmo enviando `is_internal: true`.

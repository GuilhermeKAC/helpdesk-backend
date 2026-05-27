# Request: AssignTicketRequest

**Arquivo:** `app/Http/Requests/Api/V1/AssignTicketRequest.php`  
**Usado em:** `POST /api/v1/tickets/{ticket}/assign`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `technician_id` | integer | required, exists:users,id, role in (technician, admin) |

## Validação customizada

Além de verificar existência, valida que o usuário tem role `technician` ou `admin`.  
Retorna `422` se o usuário existir mas for `customer`.

## Notas

- Acesso ao endpoint bloqueado para `customer` (403) — verificado no controller antes da validação do request

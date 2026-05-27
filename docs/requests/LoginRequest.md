# Request: LoginRequest

**Arquivo:** `app/Http/Requests/Api/V1/LoginRequest.php`  
**Usado em:** `POST /api/v1/login`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `email` | string | required, email |
| `password` | string | required |

## Erros comuns

| Situação | Status | Campo |
|----------|--------|-------|
| Email/senha inválidos | 422 | `email` |
| Conta desativada | 422 | `email` |

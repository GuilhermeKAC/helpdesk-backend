# Request: RegisterRequest

**Arquivo:** `app/Http/Requests/Api/V1/RegisterRequest.php`  
**Usado em:** `POST /api/v1/register`

---

## Regras

| Campo | Tipo | Regras |
|-------|------|--------|
| `name` | string | required, max:255 |
| `email` | string | required, email, unique:users |
| `password` | string | required, confirmed, min:8 |
| `phone` | string | nullable, max:20 |
| `role` | string | sometimes, enum UserRole |

## Notas

- `role` aceito apenas quando enviado explicitamente — padrão `customer` definido no controller
- `password_confirmation` obrigatório quando `password` presente

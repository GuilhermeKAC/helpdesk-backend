# Controller: AuthController

**Arquivo:** `app/Http/Controllers/Api/V1/AuthController.php`

---

## Endpoints

### `POST /api/v1/login`

Autentica usuário e retorna token Sanctum.

**Request:** `LoginRequest`

**Response 200:**
```json
{
  "token": "1|abc...",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "...", "email": "...", "role": "admin" }
}
```

**Erros:**
- `422` — credenciais inválidas ou conta desativada

**Efeito colateral:** atualiza `last_login_at` do usuário.

---

### `POST /api/v1/register`

Cria novo usuário. Role padrão: `customer`.

**Request:** `RegisterRequest`

**Response 201:**
```json
{
  "token": "2|xyz...",
  "token_type": "Bearer",
  "user": { ... }
}
```

---

### `POST /api/v1/logout` 🔒

Revoga o token atual (`currentAccessToken()->delete()`).

**Response 200:**
```json
{ "message": "Logout realizado com sucesso." }
```

---

### `GET /api/v1/me` 🔒

Retorna dados do usuário autenticado.

**Response 200:**
```json
{ "data": { "id": 1, "name": "...", "role": "technician", ... } }
```

---

## Resource

Retorna `UserResource` — expõe: `id`, `name`, `email`, `role`, `role_label`, `is_active`, `phone`, `created_at`.

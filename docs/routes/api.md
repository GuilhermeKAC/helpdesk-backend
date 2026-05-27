# Routes: API v1

**Prefixo:** `/api/v1`  
**Arquivo:** `routes/api.php`  
**Registro:** `bootstrap/app.php`

---

## Públicas

| Método | Endpoint | Controller | Descrição |
|--------|----------|-----------|-----------|
| POST | `/api/v1/login` | `AuthController@login` | Login — retorna token Sanctum |
| POST | `/api/v1/register` | `AuthController@register` | Registro — cria usuário customer |

---

## Protegidas (`auth:sanctum`)

### Auth

| Método | Endpoint | Controller | Descrição |
|--------|----------|-----------|-----------|
| POST | `/api/v1/logout` | `AuthController@logout` | Revoga token atual |
| GET | `/api/v1/me` | `AuthController@me` | Dados do usuário autenticado |

### Tickets

| Método | Endpoint | Controller | Role mínimo |
|--------|----------|-----------|-------------|
| GET | `/api/v1/tickets` | `TicketController@index` | any |
| POST | `/api/v1/tickets` | `TicketController@store` | any |
| GET | `/api/v1/tickets/{ticket}` | `TicketController@show` | owner/tech/admin |
| POST | `/api/v1/tickets/{ticket}/assign` | `TicketController@assign` | tech/admin |
| POST | `/api/v1/tickets/{ticket}/status` | `TicketController@changeStatus` | tech/admin |
| POST | `/api/v1/tickets/{ticket}/reply` | `TicketController@addReply` | owner/tech/admin |
| GET | `/api/v1/tickets/{ticket}/activities` | `TicketController@activities` | owner/tech/admin |

### Dashboard

| Método | Endpoint | Controller | Descrição |
|--------|----------|-----------|-----------|
| GET | `/api/v1/dashboard/stats` | `DashboardController@stats` | Métricas (cacheado 5min) |
| GET | `/api/v1/dashboard/charts` | `DashboardController@charts` | Gráficos (cacheado 5min) |

---

## Autenticação

Header obrigatório nas rotas protegidas:

```
Authorization: Bearer {token}
```

Token obtido via `POST /api/v1/login`.

---

## Controle de acesso

Implementado diretamente nos controllers via métodos `authorizeView()` e `authorizeModify()`:

| Verificação | Quem passa |
|-------------|-----------|
| `authorizeView` | admin, technician, customer (dono do ticket) |
| `authorizeModify` | admin, technician |

Retorna `403` em caso de acesso negado.

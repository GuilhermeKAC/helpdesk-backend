# Controller: TicketController

**Arquivo:** `app/Http/Controllers/Api/V1/TicketController.php`

---

## Endpoints

### `GET /api/v1/tickets` 🔒

Lista tickets com filtros e paginação. Resultado varia por role.

**Query params:**

| Param | Tipo | Descrição |
|-------|------|-----------|
| `status` | string | Filtra por status (`open`, `in_progress`, etc.) |
| `priority` | string | Filtra por prioridade |
| `category_id` | integer | Filtra por categoria |
| `search` | string | Busca full-text em título/descrição |
| `sort_by` | string | Campo de ordenação (default: `created_at`) |
| `sort_dir` | string | `asc` ou `desc` |
| `per_page` | integer | Itens por página (default: 15) |

**Isolamento por role:**
- `customer` — vê apenas próprios tickets
- `technician` — vê tickets atribuídos a si + não atribuídos
- `admin` — vê todos

**Response 200:** Coleção paginada de `TicketResource`.

---

### `POST /api/v1/tickets` 🔒

Cria novo ticket para o usuário autenticado.

**Request:** `StoreTicketRequest`

**Response 201:**
```json
{ "data": { ...TicketResource } }
```

**Efeitos colaterais:**
- `due_date` calculado via `priority.slaHours()`
- `ticket_number` gerado por trigger PG (`HD-YYYY-XXXXXX`)
- Auto-assign se categoria tiver `default_technician_id`
- Atividade `created` registrada

---

### `GET /api/v1/tickets/{ticket}` 🔒

Retorna ticket com relações carregadas.

**Relações:** `user`, `technician`, `category`, `attachments` + `replies_count`.

**Autorização:** `customer` só acessa próprios tickets → 403 caso contrário.

**Response 200:**
```json
{ "data": { ...TicketResource } }
```

---

### `POST /api/v1/tickets/{ticket}/assign` 🔒

Atribui técnico ao ticket.

**Request:** `AssignTicketRequest`

**Autorização:** `customer` → 403.

**Efeitos colaterais:**
- Status muda para `in_progress` se estava `open`
- Atividade `assigned` registrada

**Response 200:**
```json
{ "data": { ...TicketResource } }
```

---

### `POST /api/v1/tickets/{ticket}/status` 🔒

Altera status do ticket.

**Request:** `ChangeStatusRequest`

**Autorização:** `customer` → 403.

**Efeitos colaterais:**
- `resolved_at` e `resolution_time` preenchidos ao resolver
- `closed_at` preenchido ao fechar
- Atividade `status_changed` registrada

**Response 200:**
```json
{ "data": { ...TicketResource } }
```

---

### `POST /api/v1/tickets/{ticket}/reply` 🔒

Adiciona resposta ao ticket.

**Request:** `AddReplyRequest`

**Autorização:** `customer` só acessa próprios tickets → 403 caso contrário.

**Efeitos colaterais:**
- Primeiro reply não-customer preenche `response_time`
- `is_internal` forçado a `false` para customers (via `prepareForValidation`)
- Atividade `replied` registrada

**Response 201:**
```json
{ "data": { ...TicketReplyResource } }
```

---

### `GET /api/v1/tickets/{ticket}/activities` 🔒

Lista histórico de atividades do ticket em ordem cronológica.

**Autorização:** `customer` só acessa próprios tickets → 403.

**Response 200:** Coleção de `TicketActivityResource`.

---

## Autorização interna

| Método | Regra |
|--------|-------|
| `authorizeView()` | `customer` com `user_id != auth()->id` → 403 |
| `authorizeModify()` | qualquer `customer` → 403 |

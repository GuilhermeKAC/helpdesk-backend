# Service: DashboardService

**Arquivo:** `app/Services/DashboardService.php`

---

## Responsabilidade

Métricas e dados de gráficos para o dashboard. Resultados cacheados no Redis (TTL 5 min).

---

## Métodos

### `getStats(User $user): array`

Retorna estatísticas. Cache key: `dashboard:stats:{role}:{user_id}`.

**Retorno base (todos os roles):**

| Campo | Descrição |
|-------|-----------|
| `total` | Total de tickets visíveis |
| `by_status` | Contagem por status |
| `overdue` | Tickets em atraso (past `due_date`, não resolvidos) |

**Retorno adicional (admin e technician):**

| Campo | Descrição |
|-------|-----------|
| `avg_resolution_time` | Média de minutos até resolução |
| `avg_response_time` | Média de minutos até primeira resposta |
| `by_priority` | Contagem por prioridade |

---

### `getCharts(User $user): array`

Retorna dados para gráficos. Cache key: `dashboard:charts:{role}:{user_id}`.

**Retorno base (todos os roles):**

| Campo | Descrição |
|-------|-----------|
| `tickets_per_day` | Tickets criados por dia nos últimos 30 dias |

**Retorno adicional (admin e technician):**

| Campo | Descrição |
|-------|-----------|
| `by_category` | Tickets por categoria (nome + cor + total) |

---

### `flushForUser(User $user): void`

Invalida cache de stats e charts do usuário. Chamado após mutações relevantes.

---

## Cache

| Key | TTL |
|-----|-----|
| `dashboard:stats:{role}:{user_id}` | 5 min |
| `dashboard:charts:{role}:{user_id}` | 5 min |

Cache isolado por role + user — admin e customer não compartilham cache mesmo que os dados coincidam.

---

## Notas

- `baseQuery()` aplica isolamento por role igual ao `TicketService::getFilteredTickets()`
- Queries usam `DB::raw` para agregações — não passar input do usuário diretamente

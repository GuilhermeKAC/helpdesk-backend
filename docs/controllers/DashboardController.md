# Controller: DashboardController

**Arquivo:** `app/Http/Controllers/Api/V1/DashboardController.php`

---

## Endpoints

### `GET /api/v1/dashboard/stats` 🔒

Retorna estatísticas de tickets agregadas por role.

**Response 200:**
```json
{
  "data": {
    "total": 42,
    "open": 10,
    "in_progress": 8,
    "pending": 5,
    "resolved": 15,
    "closed": 4,
    "overdue": 3,
    "avg_resolution_time": 14400,
    "avg_response_time": 3600
  }
}
```

**Campos exclusivos admin/technician:** `avg_resolution_time`, `avg_response_time`, `by_priority`, `by_category`.

**Cache:** Redis, chave `dashboard:stats:{role}:{userId}`, TTL 300s.

---

### `GET /api/v1/dashboard/charts` 🔒

Retorna dados para gráficos (volume por dia, distribuição por status/prioridade).

**Response 200:**
```json
{
  "data": {
    "tickets_by_day": [...],
    "by_status": {...},
    "by_priority": {...}
  }
}
```

**Cache:** Redis, chave `dashboard:charts:{role}:{userId}`, TTL 300s.

---

## Notas

- Cache invalidado via `DashboardService::flushForUser()` — chamado após mutações relevantes em `TicketService`
- Isolamento por role aplicado em `DashboardService::getStats()` e `getCharts()`

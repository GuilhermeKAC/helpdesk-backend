# Model: TicketActivity

**Arquivo:** `app/Models/TicketActivity.php`  
**Tabela:** `ticket_activities`

---

## Fillable

`ticket_id`, `user_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`

## Casts

| Campo | Cast |
|-------|------|
| `old_value` | `array` |
| `new_value` | `array` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `ticket()` | `belongsTo(Ticket)` | Ticket relacionado |
| `user()` | `belongsTo(User)` | Autor da ação (null = sistema) |

## Actions registradas

| `action` | `old_value` | `new_value` |
|----------|-------------|-------------|
| `created` | `null` | `{status, priority}` |
| `assigned` | `{technician_id: null}` | `{technician_id: X}` |
| `status_changed` | `{status: "open"}` | `{status: "in_progress"}` |
| `replied` | `null` | `{reply_id: X, is_internal: false}` |
| `escalated` | `{priority: "medium"}` | `{priority: "high"}` |
| `closed` | `{status: "resolved"}` | `{status: "closed"}` |

## Uso

```php
// Histórico completo do ticket
$ticket->activities()->with('user')->orderBy('created_at')->get();

// Última ação
$ticket->activities()->latest()->first();
```

## Notas

- `user_id = null` → ação automática do sistema (ex: escalonamento)
- Registros são imutáveis — nunca atualizados, só inseridos

# Model: Ticket

**Arquivo:** `app/Models/Ticket.php`  
**Tabela:** `tickets`

---

## Traits

| Trait | Função |
|-------|--------|
| `SoftDeletes` | Exclusão lógica via `deleted_at` |

## Fillable

`user_id`, `technician_id`, `category_id`, `title`, `description`, `status`, `priority`, `assigned_at`, `resolved_at`, `closed_at`, `due_date`, `response_time`, `resolution_time`, `metadata`

> `ticket_number` não está em `fillable` — gerado automaticamente via trigger PostgreSQL.

## Casts

| Campo | Cast |
|-------|------|
| `status` | `TicketStatus::class` |
| `priority` | `TicketPriority::class` |
| `assigned_at` | `datetime` |
| `resolved_at` | `datetime` |
| `closed_at` | `datetime` |
| `due_date` | `datetime` |
| `metadata` | `array` |
| `response_time` | `integer` |
| `resolution_time` | `integer` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `user()` | `belongsTo(User, user_id)` | Solicitante |
| `technician()` | `belongsTo(User, technician_id)` | Técnico atribuído |
| `category()` | `belongsTo(Category)` | Categoria |
| `replies()` | `hasMany(TicketReply)` | Respostas |
| `activities()` | `hasMany(TicketActivity)` | Histórico de ações |
| `attachments()` | `morphMany(Attachment, attachable)` | Anexos diretos |

## Scopes

| Scope | Filtro |
|-------|--------|
| `scopeOpen($query)` | `status = open` |
| `scopeByStatus($query, TicketStatus)` | status específico |
| `scopeByPriority($query, TicketPriority)` | priority específica |
| `scopeForUser($query, int)` | `user_id = ?` |
| `scopeForTechnician($query, int)` | `technician_id = ?` |

## Uso

```php
// Tickets abertos urgentes de um técnico
Ticket::open()
    ->byPriority(TicketPriority::URGENT)
    ->forTechnician($user->id)
    ->with(['user', 'category'])
    ->get();

// Acessar enums com cast
$ticket->status->label();    // "Em Andamento"
$ticket->priority->slaHours(); // 24
```

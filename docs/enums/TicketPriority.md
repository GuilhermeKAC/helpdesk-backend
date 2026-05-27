# Enum: TicketPriority

**Arquivo:** `app/Enums/TicketPriority.php`  
**Tipo:** Backed Enum (`string`)  
**Cast automático:** aplicado no model `Ticket` via `casts()`

---

## Cases

| Case | Valor (`string`) | Label | Color | SLA (horas) |
|------|-----------------|-------|-------|:-----------:|
| `LOW` | `low` | Baixa | `green` | 72 |
| `MEDIUM` | `medium` | Média | `blue` | 48 |
| `HIGH` | `high` | Alta | `orange` | 24 |
| `URGENT` | `urgent` | Urgente | `red` | 4 |

---

## Métodos

### `label(): string`
Retorna o nome legível da prioridade em português.

```php
TicketPriority::LOW->label();    // "Baixa"
TicketPriority::URGENT->label(); // "Urgente"
```

### `slaHours(): int`
Retorna o prazo de SLA em horas para a prioridade.

```php
TicketPriority::URGENT->slaHours(); // 4
TicketPriority::LOW->slaHours();    // 72
```

### `color(): string`
Retorna a cor para uso em badges de UI.

```php
TicketPriority::HIGH->color();   // "orange"
TicketPriority::URGENT->color(); // "red"
```

---

## Uso no código

```php
// Calcular due_date a partir da prioridade
$dueDate = now()->addHours($ticket->priority->slaHours());

// Filtrar tickets urgentes
Ticket::byPriority(TicketPriority::URGENT)->get();

// Criar a partir de string (ex: request)
TicketPriority::from('high'); // TicketPriority::HIGH
```

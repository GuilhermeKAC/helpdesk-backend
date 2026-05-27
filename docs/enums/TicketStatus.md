# Enum: TicketStatus

**Arquivo:** `app/Enums/TicketStatus.php`  
**Tipo:** Backed Enum (`string`)  
**Cast automático:** aplicado no model `Ticket` via `casts()`

---

## Cases

| Case | Valor (`string`) | Label | Color |
|------|-----------------|-------|-------|
| `OPEN` | `open` | Aberto | `blue` |
| `IN_PROGRESS` | `in_progress` | Em Andamento | `yellow` |
| `PENDING` | `pending` | Pendente | `orange` |
| `RESOLVED` | `resolved` | Resolvido | `green` |
| `CLOSED` | `closed` | Fechado | `gray` |

---

## Métodos

### `label(): string`
Retorna o nome legível do status em português.

```php
TicketStatus::OPEN->label();       // "Aberto"
TicketStatus::IN_PROGRESS->label(); // "Em Andamento"
TicketStatus::RESOLVED->label();   // "Resolvido"
```

### `color(): string`
Retorna a cor para uso em badges de UI.

```php
TicketStatus::OPEN->color();      // "blue"
TicketStatus::RESOLVED->color();  // "green"
TicketStatus::CLOSED->color();    // "gray"
```

---

## Uso no código

```php
// Cast automático via model
$ticket->status === TicketStatus::OPEN;

// Acessar valor string
$ticket->status->value; // "open"

// Filtrar por status
Ticket::byStatus(TicketStatus::IN_PROGRESS)->get();

// Criar a partir de string (ex: request)
TicketStatus::from('open'); // TicketStatus::OPEN
```

---

## Status finais

`CLOSED` é estado final — não retorna para outros estados.  
`RESOLVED` pode ser reaberto para `OPEN` pelo admin.

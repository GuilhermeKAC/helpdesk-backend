# Model: Attachment

**Arquivo:** `app/Models/Attachment.php`  
**Tabela:** `attachments`

---

## Fillable

`attachable_type`, `attachable_id`, `user_id`, `filename`, `original_name`, `mime_type`, `size`, `path`, `disk`, `metadata`

## Casts

| Campo | Cast |
|-------|------|
| `size` | `integer` |
| `metadata` | `array` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `attachable()` | `morphTo()` | Modelo pai (Ticket ou TicketReply) |
| `user()` | `belongsTo(User)` | Quem fez o upload |

## Uso

```php
// Anexos de um ticket
$ticket->attachments;

// Anexos de uma resposta
$reply->attachments;

// Tamanho formatado (implementar no Service/Resource)
$attachment->size; // bytes brutos
```

## Notas

- `filename` = nome único gerado no upload (ex: uuid)
- `original_name` = nome exibido ao usuário
- `disk` padrão = `public` (storage local)

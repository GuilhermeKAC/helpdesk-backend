# Resource: TicketReplyResource

**Arquivo:** `app/Http/Resources/TicketReplyResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID da resposta |
| `message` | string | Conteúdo da resposta (max 10000 chars) |
| `is_internal` | boolean | Nota interna — invisível para customers |
| `user` | UserResource\|null | Autor — presente quando `load('user')` |
| `attachments` | AttachmentResource[] | Presente quando `load('attachments')` |
| `created_at` | string\|null | ISO 8601 |

## Notas

- `is_internal` sempre `false` para respostas criadas por customers (forçado em `AddReplyRequest`)
- Filtrar respostas internas para customers deve ser feito na query, não no resource

# Resource: TicketResource

**Arquivo:** `app/Http/Resources/TicketResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID interno |
| `ticket_number` | string | Número gerado por trigger PG (`HD-YYYY-XXXXXX`) |
| `title` | string | Título do ticket |
| `description` | string | Descrição |
| `status` | string | Valor do enum (`open`, `in_progress`, etc.) |
| `status_label` | string | Label legível |
| `status_color` | string | Cor hex para UI |
| `priority` | string | Valor do enum (`low`, `medium`, `high`, `urgent`) |
| `priority_label` | string | Label legível |
| `priority_color` | string | Cor hex para UI |
| `due_date` | string\|null | ISO 8601 — calculado via SLA da prioridade |
| `assigned_at` | string\|null | ISO 8601 |
| `resolved_at` | string\|null | ISO 8601 |
| `closed_at` | string\|null | ISO 8601 |
| `response_time` | integer\|null | Segundos até primeira resposta não-customer |
| `resolution_time` | integer\|null | Segundos até resolução |
| `replies_count` | integer | Presente apenas quando `loadCount('replies')` chamado |
| `user` | UserResource\|null | Criador — presente quando `load('user')` |
| `technician` | UserResource\|null | Técnico atribuído — presente quando `load('technician')` |
| `category` | CategoryResource\|null | Presente quando `load('category')` |
| `replies` | TicketReplyResource[] | Presente quando `load('replies')` |
| `attachments` | AttachmentResource[] | Presente quando `load('attachments')` |
| `created_at` | string\|null | ISO 8601 |
| `updated_at` | string\|null | ISO 8601 |

## Notas

- Campos de relação usam `whenLoaded` — só aparecem se a relação foi carregada explicitamente
- `replies_count` usa `whenCounted` — só aparece após `loadCount('replies')`
- `status_label/color` e `priority_label/color` derivados dos enums `TicketStatus` e `TicketPriority`

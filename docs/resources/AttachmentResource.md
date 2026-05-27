# Resource: AttachmentResource

**Arquivo:** `app/Http/Resources/AttachmentResource.php`

---

## Campos expostos

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID do anexo |
| `original_name` | string | Nome original do arquivo |
| `mime_type` | string | Tipo MIME (ex: `image/png`, `application/pdf`) |
| `size` | integer | Tamanho em bytes |
| `url` | string | URL pública via `Storage::disk($disk)->url($path)` |
| `created_at` | string\|null | ISO 8601 |

## Notas

- `url` gerada via `Storage::disk()` — varia conforme driver configurado (`local`, `s3`, etc.)
- Attachment é polimórfico: pode pertencer a `Ticket` ou `TicketReply`

## Usado em

- `TicketResource` — campo `attachments` (via `whenLoaded`)
- `TicketReplyResource` — campo `attachments` (via `whenLoaded`)

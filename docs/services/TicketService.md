# Service: TicketService

**Arquivo:** `app/Services/TicketService.php`

---

## Responsabilidade

Lógica de negócio de tickets. Todos os métodos rodam dentro de `DB::transaction`.

---

## Métodos

### `createTicket(array $data, User $user): Ticket`

Cria ticket, calcula `due_date` pelo SLA da prioridade, registra activity `created`.  
Se a categoria tiver `auto_assign_technician_id`, chama `assignTicket()` automaticamente.  
Chama `$ticket->refresh()` após `create()` para obter `ticket_number` gerado pelo trigger PostgreSQL.

**$data esperado:**

| Campo | Tipo | Obrigatório |
|-------|------|:-----------:|
| `category_id` | int | ✅ |
| `title` | string | ✅ |
| `description` | string | ✅ |
| `priority` | string (TicketPriority value) | ✅ |

**Retorna:** `Ticket` com `user`, `category`, `technician` carregados.

---

### `assignTicket(Ticket $ticket, int $technicianId, ?User $actor): Ticket`

Atribui técnico ao ticket. Se status for `OPEN`, muda para `IN_PROGRESS`.  
Seta `assigned_at` apenas se ainda não definido. Loga activity `assigned`.

**Retorna:** `Ticket` fresh com relacionamentos.

---

### `changeStatus(Ticket $ticket, TicketStatus $newStatus, User $actor): Ticket`

Muda status do ticket.

| Transição | Efeito extra |
|-----------|-------------|
| → `RESOLVED` | Seta `resolved_at` + calcula `resolution_time` (minutos) |
| → `CLOSED` | Seta `closed_at` |

Loga activity `status_changed` com `old_value` e `new_value`.

---

### `addReply(Ticket $ticket, array $data, User $user): TicketReply`

Cria resposta no ticket. Se `response_time` ainda nulo e autor não for `CUSTOMER`, seta o tempo de primeira resposta.  
Loga activity `replied`.

**$data esperado:**

| Campo | Tipo | Padrão |
|-------|------|--------|
| `message` | string | — |
| `is_internal` | bool | `false` |

---

### `getFilteredTickets(array $filters, User $user): LengthAwarePaginator`

Retorna tickets paginados com filtros. Isolamento por role:

| Role | Visibilidade |
|------|-------------|
| `customer` | Apenas próprios tickets |
| `technician` | Atribuídos a si + sem técnico |
| `admin` | Todos |

**Filtros disponíveis:**

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `status` | string | Filtro por `TicketStatus` |
| `priority` | string | Filtro por `TicketPriority` |
| `category_id` | int | Filtro por categoria |
| `search` | string | `ilike` em `title` e `ticket_number` |
| `sort_by` | string | `created_at`\|`updated_at`\|`priority`\|`status`\|`due_date` |
| `sort_dir` | string | `asc`\|`desc` |
| `per_page` | int | Padrão: 15 |

---

## Notas

- Todos os métodos usam `DB::transaction` — falha em qualquer passo faz rollback completo
- `logActivity()` é privado — chamado internamente após cada operação
- `ticket_number` só existe após `$ticket->refresh()` (gerado por trigger PG)

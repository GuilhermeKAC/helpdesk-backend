# HelpDesk API — Backend Technical Scope

## Stack
- PHP 8.4 / Laravel 13
- PostgreSQL (DB)
- Redis (cache, queue, session)
- Laravel Sanctum (auth)
- Laravel Horizon (queue UI)
- Spatie Permission (RBAC)
- Predis (Redis client)

---

## Status atual

| Item | Status |
|------|--------|
| Laravel 13 instalado | ✅ |
| .env configurado (Postgres + Redis) | ✅ |
| Horizon, Predis, Spatie Permission | ✅ |
| Sanctum | ❌ pendente |
| Migrations customizadas | ❌ pendente |
| Enums | ❌ pendente |
| Models | ❌ pendente |
| Controllers/Routes | ❌ pendente |
| Seeders | ❌ pendente |

---

## Roles

| Role | Permissões |
|------|-----------|
| `admin` | Tudo — usuários, categorias, fila, relatórios |
| `technician` | Ver/responder/atribuir tickets, ver dashboard |
| `customer` | Criar/ver próprios tickets, responder |

---

## Estrutura de pastas

```
app/
├── Actions/           # Lógica de negócio isolada (CreateTicket, AssignTicket...)
├── Contracts/         # Interfaces dos repositories/services
├── DTOs/              # Data Transfer Objects
├── Enums/             # TicketStatus, TicketPriority, UserRole
├── Events/            # TicketCreated, TicketAssigned, TicketStatusChanged
├── Listeners/         # Handlers dos events → disparam jobs
├── Jobs/              # SendNotification, ProcessAttachment, AutoEscalate
├── Services/          # TicketService, DashboardService
├── Repositories/      # TicketRepository, UserRepository
├── Traits/            # CacheableTrait, LogsActivity
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── AuthController
│   │   ├── TicketController
│   │   ├── AdminController
│   │   └── DashboardController
│   ├── Requests/Api/V1/  # Form Requests com validação
│   └── Resources/        # API Resources (transformação de output)
database/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder
│   ├── UserSeeder
│   └── CategorySeeder
```

---

## Passos de implementação (ordem)

### 1. Dependências faltantes
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 2. Enums (PHP 8.1+)
- `app/Enums/TicketStatus.php` → open, in_progress, pending, resolved, closed
- `app/Enums/TicketPriority.php` → low, medium, high, urgent (+ slaHours)
- `app/Enums/UserRole.php` → admin, technician, customer

### 3. Migrations (nesta ordem)
1. `add_fields_to_users_table` — role, is_active, last_login_at, phone, preferences, softDeletes
2. `create_categories_table`
3. `create_tickets_table` + PostgreSQL trigger `generate_ticket_number()`
4. `create_ticket_replies_table`
5. `create_attachments_table` — morphs (ticket ou reply)
6. `create_ticket_activities_table`

**Índices obrigatórios:**
- tickets: `[user_id, status]`, `[technician_id, status]`, `[status, priority]`
- ticket_replies: `[ticket_id, created_at]`
- GIN index em `tickets.metadata` (JSON search)
- GIN index em `ticket_replies.message` (full-text search)

### 4. Models
- `User` — HasApiTokens, Notifiable, HasRoles + casts + scopes (active, technicians)
- `Category`
- `Ticket` — boot() para ticket_number se não usar trigger, scopes por status/priority
- `TicketReply`
- `Attachment` — morphTo
- `TicketActivity`

### 5. Services & Repositories
- `TicketService::createTicket()` — valida, cria, dispara events
- `TicketService::assignTicket()` — atribui técnico, loga activity
- `TicketService::changeStatus()` — muda status, calcula resolution_time
- `TicketService::getFilteredTickets()` — filtra por role (customer vê só os seus)
- `DashboardService::getStats()` — métricas com cache Redis (TTL 5min)

### 6. Jobs
- `SendTicketNotification` → queue `emails`, tries=3, backoff=[5,10,30]s
- `ProcessTicketAttachment` → queue `default`
- `AutoEscalateTicket` → scheduled hourly
- `GenerateTicketReport` → scheduled daily 23:59

### 7. Events + Listeners
- `TicketCreated` → `SendWelcomeNotification`, `LogActivity`
- `TicketAssigned` → `NotifyTechnician`, `LogActivity`
- `TicketStatusChanged` → `NotifyCustomer`, `LogActivity`, `CalculateSLA`

### 8. Horizon config
Queues: `high`, `default`, `low`, `emails`  
Local: 5 workers | Production: 10 workers  
Balance: auto com autoScaling

### 9. Controllers + Routes
Prefixo: `/api/v1`

| Método | Rota | Auth | Role |
|--------|------|------|------|
| POST | `/login` | — | — |
| POST | `/register` | — | — |
| POST | `/logout` | sanctum | any |
| GET | `/me` | sanctum | any |
| GET/POST | `/tickets` | sanctum | any |
| GET/PUT/DELETE | `/tickets/{id}` | sanctum | owner/tech/admin |
| POST | `/tickets/{id}/assign` | sanctum | admin/tech |
| POST | `/tickets/{id}/status` | sanctum | admin/tech |
| POST | `/tickets/{id}/reply` | sanctum | any |
| GET | `/tickets/{id}/activities` | sanctum | any |
| GET | `/dashboard/stats` | sanctum | any |
| GET | `/dashboard/charts` | sanctum | any |
| * | `/admin/*` | sanctum | admin |

### 10. Seeders
- 1 admin (`admin@helpdesk.com` / `password`)
- 5 technicians (factory)
- 20 customers (factory)
- 8 categories padrão (TI, Financeiro, RH, etc.)

### 11. CORS + Sanctum
- Origins: `http://localhost:5173`, `http://localhost:3000`
- `supports_credentials: true`
- Stateful domains via `SANCTUM_STATEFUL_DOMAINS`

### 12. Scheduled commands
```
hourly       → AutoEscalateTicket
every 15min  → cache:forget tickets:stats
daily 23:59  → tickets:daily-report
every 5min   → horizon:snapshot
```

---

## Decisões técnicas

| Decisão | Motivo |
|---------|--------|
| PostgreSQL trigger para ticket_number | Garante unicidade mesmo com concorrência |
| GIN index em JSON/text | Full-text search nativo no Postgres |
| Cache com tags Redis | Invalidação granular (flush só tickets, não tudo) |
| Sanctum token-based (não session) | SPA + mobile futuro |
| Spatie Permission sobre enum role | RBAC granular sem hardcode |
| Repository pattern | Testabilidade + swap DB futuro |

---

## Não entra no escopo
- Pagamentos (Laravel Cashier foi descartado por ora)
- WebSockets realtime (fase 3)
- Multi-tenant
- 2FA
# HelpDesk — Frontend Technical Scope

## Stack
- React 18 + TypeScript + Vite
- Tailwind CSS
- TanStack Query v5 (server state)
- Zustand (client state)
- React Hook Form + Zod (forms/validation)
- Axios (HTTP)
- React Router v6
- Recharts (gráficos)
- TanStack Table (tabelas)
- React Hot Toast (notificações UI)
- Headless UI + Heroicons (componentes acessíveis)
- date-fns (datas)
- react-dropzone (upload)
- clsx + tailwind-merge (classnames)

---

## Estrutura de pastas

```
src/
├── features/
│   ├── auth/          # Login, Register, guards
│   ├── tickets/       # CRUD tickets, replies, attachments
│   ├── dashboard/     # Stats, charts por role
│   ├── admin/         # Users, categories, queue metrics
│   └── notifications/ # Centro de notificações
├── components/
│   ├── ui/            # Button, Input, Badge, Modal, Table, Spinner...
│   ├── layout/        # Sidebar, Header, PageWrapper, MobileNav
│   └── forms/         # TicketForm, ReplyForm, UserForm
├── hooks/             # useAuth, useTickets, useDebounce, useFilters
├── services/          # api.ts + endpoints por feature
├── stores/            # authStore, uiStore (Zustand)
├── types/             # ticket.types.ts, user.types.ts, api.types.ts
├── utils/             # formatDate, formatStatus, cn()
├── routes/            # PrivateRoute, RoleRoute, router config
├── config/            # constants, queryClient
└── lib/               # queryClient.ts, zodSchemas.ts
```

---

## Types principais

### ticket.types.ts
```typescript
enum TicketStatus { OPEN, IN_PROGRESS, PENDING, RESOLVED, CLOSED }
enum TicketPriority { LOW, MEDIUM, HIGH, URGENT }

interface Ticket {
  id: number
  ticket_number: string
  title: string
  description: string
  status: TicketStatus
  priority: TicketPriority
  user: User
  technician?: User
  category: Category
  replies_count: number
  created_at: string
  updated_at: string
  due_date?: string
  resolved_at?: string
}

interface TicketReply {
  id: number
  ticket_id: number
  user: User
  message: string
  is_internal: boolean
  attachments: Attachment[]
  created_at: string
}
```

### user.types.ts
```typescript
enum UserRole { ADMIN = 'admin', TECHNICIAN = 'technician', CUSTOMER = 'customer' }

interface User {
  id: number
  name: string
  email: string
  role: UserRole
  is_active: boolean
  phone?: string
  created_at: string
}
```

---

## Rotas

| Path | Componente | Role |
|------|-----------|------|
| `/login` | AuthPage | público |
| `/register` | AuthPage | público |
| `/dashboard` | DashboardPage | todos |
| `/tickets` | TicketListPage | todos |
| `/tickets/new` | TicketCreatePage | todos |
| `/tickets/:id` | TicketDetailPage | todos |
| `/admin/users` | UsersPage | admin |
| `/admin/categories` | CategoriesPage | admin |
| `/admin/queue` | QueueMetricsPage | admin |
| `/profile` | ProfilePage | todos |

Route guards: `PrivateRoute` (auth) + `RoleRoute` (role check)

---

## Estado global (Zustand)

### authStore
```typescript
interface AuthStore {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  login(credentials): Promise<void>
  logout(): void
  setUser(user: User): void
}
```

### uiStore
```typescript
interface UIStore {
  sidebarOpen: boolean
  toggleSidebar(): void
  activeFilters: TicketFilters
  setFilters(filters): void
}
```

---

## Server state (TanStack Query)

### Queries
- `useTickets(filters)` — lista com paginação + cache 5min
- `useTicket(id)` — detalhe + replies
- `useCategories()` — cache 30min (muda pouco)
- `useUsers(filters)` — admin only
- `useDashboardStats()` — cache 5min
- `useDashboardCharts()` — cache 5min

### Mutations
- `useCreateTicket()` → invalidate `tickets`
- `useUpdateTicket()` → invalidate `ticket + tickets`
- `useAssignTicket()` → invalidate `ticket`
- `useChangeStatus()` → invalidate `ticket + stats`
- `useAddReply()` → invalidate `ticket`

---

## Features por role

### Customer
- Dashboard: próprios tickets, status breakdown
- Criar ticket (título, descrição, categoria, prioridade, attachments)
- Ver histórico de tickets com filtros (status, data)
- Responder ticket aberto
- Ver timeline de atividades

### Technician
- Dashboard: tickets atribuídos, SLA alerts
- Lista geral de tickets com filtros avançados
- Atribuir ticket a si mesmo
- Mudar status do ticket
- Resposta interna (flag `is_internal`)
- Upload de attachments

### Admin
- Dashboard completo (métricas, gráficos, top técnicos)
- Gerenciar usuários (criar, editar, ativar/desativar, trocar role)
- Gerenciar categorias (cor, ícone, SLA, técnico padrão)
- Ver métricas do Horizon (filas, jobs, falhas)
- Limpar cache via UI
- Exportar relatórios

---

## Componentes UI

### Primitivos
- `Button` — variants: primary, secondary, ghost, danger
- `Input`, `Textarea`, `Select`, `Checkbox`
- `Badge` — cores mapeadas por status/priority
- `Modal` — Headless UI Dialog
- `Spinner`, `Skeleton`
- `Avatar` — iniciais ou foto
- `Tooltip` — Headless UI

### Compostos
- `DataTable` — TanStack Table, sorting, pagination, row selection
- `FileUpload` — react-dropzone, preview, progress
- `StatusSelect` — dropdown com cores
- `PriorityBadge` — ícone + cor por priority
- `TicketCard` — card compacto para listas
- `ActivityTimeline` — histórico de ações do ticket
- `StatsCard` — número grande + label + trend
- `ChartCard` — wrapper Recharts com loading state

---

## Passos de implementação

1. Criar projeto Vite + TS + instalar deps
2. Configurar Tailwind
3. Criar `src/config/queryClient.ts` + `src/services/api.ts`
4. Criar types completos
5. Criar Zustand stores (auth, ui)
6. Criar componentes UI primitivos
7. Criar layout (Sidebar, Header)
8. Criar route structure + guards
9. Feature auth (login/register/logout)
10. Feature tickets (list, create, detail, reply)
11. Feature dashboard (stats + charts por role)
12. Feature admin (users, categories, queue)
13. Notificações toast + error boundaries

---

## Decisões técnicas

| Decisão | Motivo |
|---------|--------|
| TanStack Query (não Redux) | Server state ≠ client state; cache automático |
| Zustand (não Context) | Menos boilerplate, sem re-renders desnecessários |
| Zod schemas centralizados | Validação client = validação server (espelhado) |
| feature-based folders | Escala melhor que type-based (components/pages/hooks) |
| Headless UI | Acessibilidade nativa sem opinionated styling |
| TanStack Table | Virtual rows, sorting/filtering client-side gratuito |

---

## Não entra no escopo
- WebSockets (fase 3)
- Dark mode
- i18n
- PWA / mobile app
- Testes E2E (fase posterior)
# Model: User

**Arquivo:** `app/Models/User.php`  
**Tabela:** `users`

---

## Traits

| Trait | Pacote | Função |
|-------|--------|--------|
| `HasApiTokens` | Laravel Sanctum | Emissão e revogação de tokens |
| `HasFactory` | Laravel | Factory para testes |
| `HasRoles` | Spatie Permission | RBAC granular |
| `Notifiable` | Laravel | Envio de notificações |
| `SoftDeletes` | Laravel | Exclusão lógica |

## Fillable

`name`, `email`, `password`, `role`, `is_active`, `phone`, `preferences`

## Hidden

`password`, `remember_token`

## Casts

| Campo | Cast |
|-------|------|
| `email_verified_at` | `datetime` |
| `last_login_at` | `datetime` |
| `password` | `hashed` |
| `is_active` | `boolean` |
| `preferences` | `array` |
| `role` | `UserRole::class` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `tickets()` | `hasMany(Ticket, user_id)` | Tickets criados pelo usuário |
| `assignedTickets()` | `hasMany(Ticket, technician_id)` | Tickets atribuídos (técnico) |
| `replies()` | `hasMany(TicketReply)` | Respostas do usuário |
| `activities()` | `hasMany(TicketActivity)` | Atividades registradas |

## Scopes

| Scope | Filtro |
|-------|--------|
| `scopeActive($query)` | `is_active = true` |
| `scopeTechnicians($query)` | `role = technician` |
| `scopeCustomers($query)` | `role = customer` |

## Uso

```php
// Listar técnicos ativos
User::active()->technicians()->get();

// Verificar role via cast
$user->role === UserRole::ADMIN;

// Criar token Sanctum
$token = $user->createToken('api')->plainTextToken;
```

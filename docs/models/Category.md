# Model: Category

**Arquivo:** `app/Models/Category.php`  
**Tabela:** `categories`

---

## Fillable

`name`, `description`, `color`, `icon`, `auto_assign_technician_id`, `sla_hours`, `is_active`

## Casts

| Campo | Cast |
|-------|------|
| `is_active` | `boolean` |
| `sla_hours` | `integer` |

## Relacionamentos

| Método | Tipo | Descrição |
|--------|------|-----------|
| `autoAssignTechnician()` | `belongsTo(User, auto_assign_technician_id)` | Técnico padrão da categoria |
| `tickets()` | `hasMany(Ticket)` | Tickets desta categoria |

## Scopes

| Scope | Filtro |
|-------|--------|
| `scopeActive($query)` | `is_active = true` |

## Uso

```php
// Listar categorias ativas
Category::active()->get();

// Obter SLA da categoria
$category->sla_hours; // 48

// Técnico padrão (se configurado)
$category->autoAssignTechnician; // User|null
```

# Enum: UserRole

**Arquivo:** `app/Enums/UserRole.php`  
**Tipo:** Backed Enum (`string`)  
**Cast automático:** aplicado no model `User` via `casts()`

---

## Cases

| Case | Valor (`string`) | Label |
|------|-----------------|-------|
| `ADMIN` | `admin` | Administrador |
| `TECHNICIAN` | `technician` | Técnico |
| `CUSTOMER` | `customer` | Cliente |

---

## Métodos

### `label(): string`
Retorna o nome legível do role em português.

```php
UserRole::ADMIN->label();      // "Administrador"
UserRole::TECHNICIAN->label(); // "Técnico"
UserRole::CUSTOMER->label();   // "Cliente"
```

---

## Permissões por role

| Ação | ADMIN | TECHNICIAN | CUSTOMER |
|------|:-----:|:----------:|:--------:|
| Criar ticket | ✅ | ✅ | ✅ |
| Ver todos os tickets | ✅ | ✅ | ❌ |
| Ver próprios tickets | ✅ | ✅ | ✅ |
| Atribuir ticket | ✅ | ✅ | ❌ |
| Mudar status | ✅ | ✅ | ❌ |
| Resposta interna | ✅ | ✅ | ❌ |
| Gerenciar usuários | ✅ | ❌ | ❌ |
| Gerenciar categorias | ✅ | ❌ | ❌ |
| Ver métricas de fila | ✅ | ❌ | ❌ |

---

## Uso no código

```php
// Cast automático via model
$user->role === UserRole::ADMIN;

// Scope de filtragem
User::technicians()->get();
User::customers()->get();

// Criar a partir de string (ex: request)
UserRole::from('technician'); // UserRole::TECHNICIAN
```

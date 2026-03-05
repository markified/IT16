# Role-Based Access Control (RBAC) System

## Overview
This application implements a comprehensive Role-Based Access Control (RBAC) system to manage user permissions and access to different features and resources.

**IMPORTANT SECURITY NOTE**: 
- The highest privilege role is `'superadmin'` (stored as the exact string 'superadmin' in the database)
- Superadmin has FULL access to all system features and settings
- The middleware is named 'superadmin' and strictly checks for the 'superadmin' role ONLY
- No other role has the same level of access as superadmin

## Roles

The system supports four distinct user roles:

### 1. Superadmin
- **Full System Access**: Complete control over all features and settings
- **User Management**: Create, edit, and delete user accounts
- **Security Management**: Access to security settings, login history, and session management
- **Database Management**: Backup, restore, and optimize database
- **Inventory Management**: Full access to all inventory features
- **All Reports**: Access to all reporting features

### 2. Inventory
- **Inventory Operations**: Manage products, categories, and suppliers
- **Stock Management**: Handle stock in, stock out, and adjustments
- **Inventory Reports**: View and generate inventory-related reports
- **Limited Administrative Access**: No access to user management or system settings

### 3. Security
- **Audit Trail**: View and monitor audit logs
- **Login History**: Access login history and security events
- **Limited Access**: No access to inventory or system administration

### 4. Employee
- **Basic Access**: View dashboard only
- **No Management Features**: Cannot modify data or access administrative features

## Access Control Matrix

| Feature                    | Superadmin | Inventory | Security | Employee |
|---------------------------|------------|-----------|----------|----------|
| Dashboard                 | ✓          | ✓         | ✗        | ✓        |
| Products Management       | ✓          | ✓         | ✗        | ✗        |
| Categories Management     | ✓          | ✓         | ✗        | ✗        |
| Suppliers Management      | ✓          | ✓         | ✗        | ✗        |
| Stock In                  | ✓          | ✓         | ✗        | ✗        |
| Stock Out                 | ✓          | ✓         | ✗        | ✗        |
| Stock Adjustments         | ✓          | ✓         | ✗        | ✗        |
| Inventory Reports         | ✓          | ✓         | ✗        | ✗        |
| Audit Logs                | ✓          | ✗         | ✗        | ✗        |
| User Management           | ✓          | ✗         | ✗        | ✗        |
| Custom Reports            | ✓          | ✗         | ✗        | ✗        |
| Security Settings         | ✓          | ✗         | ✓        | ✗        |
| Database Management       | ✓          | ✗         | ✓        | ✗        |

## Implementation

### 1. Middleware

#### Superadmin Middleware (AdminMiddleware)
```php
// Restricts access to SUPERADMIN role ONLY
Route::middleware('superadmin')->group(function () {
    // Routes only accessible by users with 'superadmin' role
    // Includes: User Management, Database Management, Security Management
});
```

#### InventoryMiddleware
```php
// Allows superadmin and inventory roles
Route::middleware('inventory')->group(function () {
    // Routes accessible by inventory managers and superadmin
    // Includes: Products, Suppliers, Categories, Stock In/Out, Adjustments, Reports
});
```

#### SecurityMiddleware
```php
// Allows superadmin and security roles for Security and Database management
Route::middleware('role:superadmin,security')->group(function () {
    // Routes accessible by security personnel and superadmin
    // Includes: Security Settings, Database Management
});
```

#### RoleMiddleware (Flexible)
```php
// Accepts multiple roles as parameters
Route::middleware('role:superadmin,inventory')->group(function () {
    // Routes accessible by specified roles
});
```

### 2. User Model Helper Methods

```php
// Check specific role
$user->isAdmin()           // Returns true if superadmin
$user->isInventory()       // Returns true if inventory
$user->isSecurity()        // Returns true if security
$user->isEmployee()        // Returns true if employee

// Check multiple roles
$user->hasRole('superadmin')              // Single role check
$user->hasRole(['superadmin', 'inventory']) // Multiple roles check
$user->hasAnyRole(['superadmin', 'inventory']) // Check if has any of the roles

// Permission checks
$user->canManageInventory()  // Returns true if superadmin or inventory
$user->canManageSecurity()   // Returns true if superadmin or security
```

### 3. Blade Directives

Custom Blade directives for use in views:

```blade
{{-- Check for specific role --}}
@role('superadmin')
    <a href="{{ route('users.index') }}">Manage Users</a>
@endrole

{{-- Check for any of multiple roles --}}
@hasanyrole(['superadmin', 'inventory'])
    <a href="{{ route('products') }}">Products</a>
@endhasanyrole

{{-- Shorthand directives --}}
@superadmin
    <button>Admin Only Button</button>
@endsuperadmin

@inventory
    <a href="{{ route('stock-in.index') }}">Stock In</a>
@endinventory

@security
    <a href="{{ route('audit-logs.index') }}">Audit Logs</a>
@endsecurity
```

### 4. Route Protection

Routes are organized and protected by middleware:

```php
// Superadmin only routes
Route::middleware('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::prefix('database')->group(function () { /* ... */ });
    Route::prefix('security')->group(function () { /* ... */ });
});

// Inventory management routes
Route::middleware('inventory')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::prefix('stock-in')->group(function () { /* ... */ });
    Route::prefix('inventory-issues')->group(function () { /* ... */ });
});

// Security routes
Route::middleware('security')->group(function () {
    Route::prefix('audit-logs')->group(function () { /* ... */ });
});
```

## Best Practices

### 1. Always Protect Routes
Never expose sensitive routes without proper middleware protection.

```php
// ❌ BAD - No protection
Route::get('/users', [UserController::class, 'index']);

// ✅ GOOD - Protected with middleware
Route::middleware('admin')->get('/users', [UserController::class, 'index']);
```

### 2. Use Helper Methods
Prefer helper methods over direct role comparison for better maintainability.

```php
// ❌ BAD
if (auth()->user()->role === 'superadmin') { }

// ✅ GOOD
if (auth()->user()->isAdmin()) { }
if (auth()->user()->canManageInventory()) { }
```

### 3. Protect Views
Hide UI elements that users don't have access to.

```blade
{{-- ❌ BAD - Shows button user can't access --}}
<a href="{{ route('users.index') }}">Users</a>

{{-- ✅ GOOD - Conditionally shows based on permission --}}
@superadmin
    <a href="{{ route('users.index') }}">Users</a>
@endsuperadmin
```

### 4. Fail Securely
Always deny access by default and explicitly allow when appropriate.

```php
// ❌ BAD - Allows by default
if ($user->role !== 'employee') {
    // Allow access
}

// ✅ GOOD - Denies by default
if ($user->canManageInventory()) {
    // Allow access
} else {
    abort(403);
}
```

## Role Assignment

### During Registration
Users can register with limited roles (inventory or security):
```php
'role' => 'required|in:inventory,security'
```

### By Superadmin
Superadmins can assign any role when creating users:
```php
'role' => 'required|in:superadmin,employee,inventory,security'
```

## Security Considerations

1. **Principle of Least Privilege**: Users are granted only the minimum permissions needed
2. **Defense in Depth**: Multiple layers of protection (middleware, controller checks, view conditionals)
3. **Explicit Permissions**: Permissions are explicitly defined, not implicitly assumed
4. **Audit Trail**: All actions are logged for security and compliance
5. **Role Immutability**: Users cannot change their own roles

## Testing Role-Based Access

### Manual Testing Checklist
- [ ] Superadmin can access all features
- [ ] Inventory users can only access inventory features
- [ ] Security users can only access audit logs
- [ ] Employees can only view dashboard
- [ ] Direct URL access to restricted routes returns 403
- [ ] Navigation menus show only allowed items for each role

### Test User Accounts
Create test accounts for each role to verify access control:

```bash
php artisan tinker
```

```php
// Create test users
User::create([
    'name' => 'Test Inventory',
    'email' => 'inventory@test.com',
    'password' => bcrypt('password'),
    'role' => 'inventory'
]);

User::create([
    'name' => 'Test Security',
    'email' => 'security@test.com',
    'password' => bcrypt('password'),
    'role' => 'security'
]);

User::create([
    'name' => 'Test Employee',
    'email' => 'employee@test.com',
    'password' => bcrypt('password'),
    'role' => 'employee'
]);
```

## Troubleshooting

### User Cannot Access Expected Features
1. Verify user's role in the database: `SELECT role FROM users WHERE email = 'user@example.com';`
2. Check middleware is applied to the route: Review `routes/web.php`
3. Clear application cache: `php artisan cache:clear`
4. Check for typos in role names (case-sensitive)

### 403 Forbidden Errors
1. Confirm user is authenticated
2. Verify role matches middleware requirements
3. Check if route uses correct middleware
4. Review middleware logic in `app/Http/Middleware/`

### Navigation Items Not Showing/Hiding
1. Clear view cache: `php artisan view:clear`
2. Verify Blade directive syntax
3. Check user role in database
4. Ensure user is authenticated

## Future Enhancements

Potential improvements to the RBAC system:

1. **Permission-Based Access**: Move from role-based to permission-based control
2. **Role Hierarchy**: Implement role inheritance
3. **Dynamic Permissions**: Allow runtime permission assignment
4. **API Token Roles**: Extend role system to API authentication
5. **Multi-Role Support**: Allow users to have multiple roles simultaneously
6. **Role-Based Data Filtering**: Filter data based on user role/department

## Changelog

### Version 1.0 (March 4, 2026)
- Initial RBAC implementation
- Four distinct roles: Superadmin, Inventory, Security, Employee
- Middleware-based route protection
- Custom Blade directives
- Helper methods in User model
- Comprehensive access control matrix

---

**Last Updated**: March 4, 2026  
**Maintained By**: Development Team

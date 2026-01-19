Since we are following TDD, Claude recommend starting with the authentication tests first, then working through models → policies → API endpoints → frontend.

# Acceptance Criteria: Laravel CRM with Vue 3 + PrimeVue

**Project**: Simple CRM System for Managing Clients  
**Approach**: TDD with Laravel Backend + Inertia.js + Vue 3 + PrimeVue Frontend  
**Authentication**: Sanctum SPA (Cookie-based)  
**Testing Focus**: Backend Laravel tests (Feature + Unit)

---

## 1. Project Setup & Configuration

### AC 1.1: Laravel + Inertia + Vue 3 Setup
- [X] Fresh Laravel 11.x installation
- [X] Inertia.js installed and configured
- [X] Vue 3 with `<script setup>` syntax
- [X] PrimeVue installed with theme configuration
- [X] Vite configured for Vue SFC compilation
- [X] PrimeIcons available globally

**Test**: `php artisan about` shows correct versions

---

## 2. Database Design & Models

### AC 2.1: User Model Enhancement
- [X] Users table has: `name`, `email`, `email_verified_at`, `password`, `remember_token`, `deleted_at`
- [X] User model uses `SoftDeletes` trait
- [hold] User model has `HasFactory` and `Notifiable` traits
- [hold] Email verification implemented via `MustVerifyEmail` interface

**Tests**:
```php
test('user can be soft deleted')
test('user has email verified at timestamp')
```

### AC 2.2: Client Model
- [X] Clients table: `name`, `email`, `phone`, `company`, `address`, `status` (active/inactive), `deleted_at`, timestamps
- [X] Client model uses `SoftDeletes`
- [X] Client has `status` scope for active clients: `scopeActive($query)`
- [X] Client has accessor for formatted dates: `getCreatedAtAttribute()` returns m/d/Y format
- [X] Client `hasMany` Projects
- [ ] Client `morphMany` Media (Spatie)

**Tests**:
```php
test('can create client with valid data') 
test('active scope returns only active clients')
test('created_at accessor returns formatted date')
test('client has many projects relationship')
test('client can have media attachments')
test('soft deleted clients are not in default queries')
```

### AC 2.3: Project Model
- [ ] Projects table: `name`, `description`, `client_id`, `user_id` (assigned to), `status` (planning/in_progress/completed/on_hold), `start_date`, `deadline`, `deleted_at`, timestamps
- [ ] Project model uses `SoftDeletes`
- [ ] Project `belongsTo` Client
- [ ] Project `belongsTo` User (assigned user)
- [ ] Project `hasMany` Tasks
- [ ] Project `morphMany` Media (Spatie)
- [ ] Date accessors for `start_date` and `deadline` in m/d/Y format
- [ ] Status scope: `scopeInProgress($query)`

**Tests**:
```php
test('can create project with valid data')
test('project belongs to client')
test('project belongs to user')
test('project has many tasks')
test('project can have media attachments')
test('date accessors return formatted dates')
test('in_progress scope filters correctly')
```

### AC 2.4: Task Model
- [ ] Tasks table: `title`, `description`, `project_id`, `user_id` (assigned to), `status` (todo/in_progress/done), `priority` (low/medium/high), `due_date`, `deleted_at`, timestamps
- [ ] Task model uses `SoftDeletes`
- [ ] Task `belongsTo` Project
- [ ] Task `belongsTo` User (assigned user)
- [ ] Task `morphMany` Media (Spatie)
- [ ] Date accessor for `due_date` in m/d/Y format
- [ ] Status scopes: `scopePending($query)`, `scopeCompleted($query)`

**Tests**:
```php
test('can create task with valid data')
test('task belongs to project')
test('task belongs to user')
test('task can have media attachments')
test('due_date accessor returns formatted date')
test('pending scope excludes completed tasks')
```

---

## 3. Authentication & Authorization

### AC 3.1: Sanctum SPA Authentication Setup
- [ ] Sanctum installed and configured
- [ ] `EnsureFrontendRequestsAreStateful` middleware in place
- [ ] CORS configured for SPA
- [ ] CSRF cookie route available
- [ ] Session driver set to `cookie` or `database`
- [ ] `SANCTUM_STATEFUL_DOMAINS` configured in .env

**Tests**:
```php
test('can get csrf cookie')
test('unauthenticated requests return 401')
```

### AC 3.2: Login & Registration
- [ ] POST `/login` endpoint accepts email/password, returns user data
- [ ] POST `/register` endpoint creates user, sends verification email
- [ ] POST `/logout` endpoint destroys session
- [ ] GET `/user` endpoint returns authenticated user
- [ ] Login attempts rate limited (5 per minute)
- [ ] Validation errors return 422 with proper structure

**Tests**:
```php
test('user can register with valid credentials')
test('user can login with valid credentials')
test('user cannot login with invalid credentials')
test('user can logout')
test('authenticated user can fetch their data')
test('login is rate limited after 5 attempts')
```

### AC 3.3: Email Verification
- [ ] Verification email sent on registration
- [ ] GET `/email/verify/{id}/{hash}` verifies email
- [ ] POST `/email/verification-notification` resends verification
- [ ] Unverified users redirected/blocked from protected routes
- [ ] Mailtrap configured for local testing

**Tests**:
```php
test('verification email sent on registration')
test('user can verify email with valid link')
test('user cannot access protected routes without verification')
test('user can request new verification email')
```

### AC 3.4: Roles & Permissions (Spatie)
- [X] Spatie Permission package installed
- [X] Two roles seeded: `admin`, `user`
- [X] Permissions seeded: `manage-users`, `manage-clients`, `manage-projects`, `manage-tasks`, `view-own-projects`, `view-own-tasks`
- [X] Admin has all permissions
- [X] User has `view-own-projects`, `view-own-tasks`
- [ ] Middleware: `role:admin` and `permission:manage-clients`

**Tests**:
```php
test('admin role has all permissions')
test('user role has limited permissions')
test('admin can access admin routes')
test('user cannot access admin routes')
test('permissions are checked on API endpoints')
```

### AC 3.5: Gates & Policies
- [ ] ClientPolicy: `viewAny`, `view`, `create`, `update`, `delete`
- [ ] ProjectPolicy: `viewAny`, `view`, `create`, `update`, `delete` (users can only view their assigned projects)
- [ ] TaskPolicy: `viewAny`, `view`, `create`, `update`, `delete` (users can only view their assigned tasks)
- [ ] Policies registered in `AuthServiceProvider`

**Tests**:
```php
test('admin can view any client')
test('admin can create client')
test('user can view assigned project')
test('user cannot view unassigned project')
test('user cannot delete project')
test('policy denies access return 403')
```

---

## 4. API Endpoints

### AC 4.1: Client API Endpoints
- [X] GET `/api/clients` - list clients (paginated, filterable by status)
- [X] GET `/api/clients/{client}` - show client with projects
- [X] POST `/api/clients` - create client
- [X] PUT `/api/clients/{client}` - update client
- [] DELETE `/api/clients/{client}` - soft delete client
- [] POST `/api/clients/{client}/media` - upload client documents
- [ ] All endpoints use route model binding
- [ ] All endpoints return `ClientResource`
- [ ] All endpoints check policies

**Tests**:
```php
test('admin can list all clients')
test('user cannot list clients without permission')
test('can show single client with projects')
test('can create client with valid data')
test('cannot create client with invalid data')
test('can update client')
test('can soft delete client')
test('can upload media to client')
test('api returns proper json structure')
```

### AC 4.2: Project API Endpoints
- [ ] GET `/api/projects` - list projects (user sees only assigned, admin sees all)
- [ ] GET `/api/projects/{project}` - show project with client, tasks, assigned user
- [ ] POST `/api/projects` - create project
- [ ] PUT `/api/projects/{project}` - update project
- [ ] DELETE `/api/projects/{project}` - soft delete project
- [ ] POST `/api/projects/{project}/media` - upload project files
- [ ] All endpoints return `ProjectResource`

**Tests**:
```php
test('admin can list all projects')
test('user can list only assigned projects')
test('can show single project with relationships')
test('can create project assigned to user')
test('can update project status')
test('can soft delete project')
test('can upload media to project')
```

### AC 4.3: Task API Endpoints
- [ ] GET `/api/tasks` - list tasks (filtered by project_id, user sees only assigned)
- [ ] GET `/api/tasks/{task}` - show task with project
- [ ] POST `/api/tasks` - create task
- [ ] PUT `/api/tasks/{task}` - update task
- [ ] DELETE `/api/tasks/{task}` - soft delete task
- [ ] POST `/api/tasks/{task}/media` - upload task attachments
- [ ] All endpoints return `TaskResource`

**Tests**:
```php
test('admin can list all tasks')
test('user can list only assigned tasks')
test('can filter tasks by project')
test('can create task with valid data')
test('can update task status')
test('can soft delete task')
test('can upload media to task')
```

### AC 4.4: User Management API (Admin Only)
- [ ] GET `/api/users` - list all users
- [ ] GET `/api/users/{user}` - show user
- [ ] POST `/api/users` - create user (admin only)
- [ ] PUT `/api/users/{user}` - update user
- [ ] DELETE `/api/users/{user}` - soft delete user
- [ ] PUT `/api/users/{user}/roles` - assign roles
- [ ] All endpoints return `UserResource`

**Tests**:
```php
test('admin can list all users')
test('admin can create user')
test('admin can assign roles to user')
test('user cannot access user management')
```

### AC 4.5: API Error Handling
- [ ] 404 errors return JSON: `{"message": "Not found"}`
- [ ] 403 errors return JSON: `{"message": "Unauthorized"}`
- [ ] 422 validation errors return JSON with field errors
- [ ] 500 errors return JSON: `{"message": "Server error"}` (in production)
- [ ] Try-catch blocks in controllers for database operations
- [ ] Custom exception handler for API routes

**Tests**:
```php
test('api returns 404 json for missing resources')
test('api returns 403 json for unauthorized access')
test('api returns 422 json for validation errors')
test('api handles database exceptions gracefully')
```

---

## 5. Database Seeders & Factories

### AC 5.1: Factories
- [ ] UserFactory creates users with verified emails
- [ ] ClientFactory creates clients with realistic data (Faker)
- [ ] ProjectFactory creates projects with valid status and dates
- [ ] TaskFactory creates tasks with valid priority and status

**Tests**:
```php
test('user factory creates valid user')
test('client factory creates valid client')
test('project factory creates valid project with relationships')
test('task factory creates valid task with relationships')
```

### AC 5.2: Seeders
- [ ] DatabaseSeeder runs all seeders
- [ ] RolePermissionSeeder creates roles and permissions
- [ ] UserSeeder creates:
    - Admin user: admin@example.com / password
    - Regular user: user@example.com / password
    - Both with verified emails and assigned roles
- [ ] ClientSeeder creates 20 clients (15 active, 5 inactive)
- [ ] ProjectSeeder creates 30 projects assigned to users
- [ ] TaskSeeder creates 50 tasks across projects

**Tests**:
```php
test('database seeder runs without errors')
test('admin user exists after seeding')
test('clients are seeded with correct counts')
test('projects have assigned users')
```

---

## 6. Frontend (Inertia + Vue 3 + PrimeVue)

### AC 6.1: Authentication Pages
- [ ] Login page (`/login`) with PrimeVue `InputText` and `Button`
- [ ] Register page (`/register`) with form validation
- [ ] Email verification notice page
- [ ] Forgot password page (optional for junior level)
- [ ] Route redirect: `/` → `/login` for guests

**Components**:
- `Pages/Auth/Login.vue` with PrimeVue Card, InputText, Password, Button
- Error messages displayed with PrimeVue Message component
- Loading states on button clicks

### AC 6.2: Dashboard Layout
- [ ] Authenticated layout with PrimeVue Menubar or Sidebar
- [ ] Navigation items: Dashboard, Clients, Projects, Tasks, Users (admin only)
- [ ] User menu with logout button
- [ ] Responsive design with PrimeVue breakpoint utilities

**Components**:
- `Layouts/AuthenticatedLayout.vue`
- PrimeVue Menubar with dropdown for user menu
- Role-based navigation visibility

### AC 6.3: Clients Pages
- [ ] Clients index page with PrimeVue DataTable
    - [X] Columns: Name, Email, Company, Status, Actions
    - [ ] Pagination (server-side via Inertia)
    - [ ] Filter by status (active/inactive) with Dropdown
    - [ ] Search by name/email with InputText (debounced)
    - [ ] Create button opens Dialog
- [ ] Client show page with TabView
    - Overview tab: client details
    - Projects tab: nested DataTable
    - Documents tab: file upload with FileUpload component
- [ ] Create/Edit client in Dialog with form validation
- [ ] Delete confirmation with ConfirmDialog

**Components**:
- `Pages/Clients/Index.vue`
- `Pages/Clients/Show.vue`
- `Components/Clients/ClientForm.vue`
- PrimeVue: DataTable, Dialog, TabView, FileUpload, ConfirmDialog

### AC 6.4: Projects Pages
- [ ] Projects index page with PrimeVue DataTable
    - Columns: Name, Client, Assigned To, Status, Deadline, Actions
    - Status filter with MultiSelect
    - Project status badges with Tag component
    - Users see only their assigned projects
- [ ] Project show page with TabView
    - Overview: project details with client info
    - Tasks: nested DataTable with inline status updates
    - Files: media library with FileUpload
- [ ] Create/Edit project form in Dialog
    - Client Dropdown (searchable)
    - User assignment Dropdown
    - Date pickers with Calendar component
- [ ] Status update workflow with ConfirmDialog

**Components**:
- `Pages/Projects/Index.vue`
- `Pages/Projects/Show.vue`
- `Components/Projects/ProjectForm.vue`
- PrimeVue: DataTable, Tag, Calendar, Dropdown, AutoComplete

### AC 6.5: Tasks Pages
- [ ] Tasks index page with DataTable
    - Columns: Title, Project, Assigned To, Priority, Status, Due Date, Actions
    - Filter by project with Dropdown
    - Priority badges with Tag (color-coded)
    - Drag-and-drop status updates (bonus)
- [ ] Task show page with details and comments (simplified)
- [ ] Create/Edit task form in Dialog
    - Project Dropdown
    - Priority SelectButton
    - Status Dropdown
    - Due date Calendar
- [ ] Quick status update from DataTable with Dropdown

**Components**:
- `Pages/Tasks/Index.vue`
- `Pages/Tasks/Show.vue`
- `Components/Tasks/TaskForm.vue`
- PrimeVue: DataTable, Tag, SelectButton, Dropdown, Calendar

### AC 6.6: Users Management (Admin Only)
- [ ] Users index page with DataTable
    - Columns: Name, Email, Role, Verified, Actions
    - Role filter with MultiSelect
- [ ] Create/Edit user form in Dialog
    - Role assignment with Dropdown
    - Email verification checkbox
- [ ] Role management with Chips component

**Components**:
- `Pages/Users/Index.vue`
- `Components/Users/UserForm.vue`
- PrimeVue: DataTable, Chips, Dropdown, Checkbox

---

## 7. Email & Notifications

### AC 7.1: Email Verification Mail
- [ ] Verification email uses Laravel Mailable
- [ ] Email contains verification link
- [ ] Email sent via queue (optional for junior)
- [ ] Mailtrap configured for testing

**Tests**:
```php
test('verification email is sent on registration')
test('verification email contains correct link')
```

### AC 7.2: Notification System
- [ ] Project assignment notification (email)
- [ ] Task assignment notification (email)
- [ ] Notifications use Laravel Notification system
- [ ] Notifications sent to assigned users

**Tests**:
```php
test('user receives email when assigned to project')
test('user receives email when assigned to task')
```

---

## 8. Testing Requirements

### AC 8.1: Feature Tests Coverage
- [ ] All API endpoints have feature tests
- [ ] Authentication flow tested
- [ ] Authorization/policy tests
- [ ] CRUD operations tested for all resources
- [ ] File upload tests with fake storage
- [ ] Minimum 80% code coverage

### AC 8.2: Unit Tests
- [ ] Model accessor tests
- [ ] Model scope tests
- [ ] Model relationship tests
- [ ] Policy logic tests
- [ ] Validation rule tests (custom rules if any)

### AC 8.3: Test Organization
- [ ] Tests use database transactions or RefreshDatabase
- [ ] Tests use factories for data generation
- [ ] Tests named with `test_` prefix or `test()` helper
- [ ] Tests organized in Feature/ and Unit/ directories

---

## 9. Additional Requirements

### AC 9.1: Custom Error Pages (Inertia)
- [ ] 404 error page with PrimeVue styling
- [ ] 403 unauthorized page
- [ ] 500 server error page
- [ ] Error pages use shared layout

### AC 9.2: Spatie Media Library
- [ ] Media library configured for clients, projects, tasks
- [ ] Media collections: 'documents', 'images'
- [ ] File validation (max size, allowed types)
- [ ] Media displayed in frontend with PrimeVue Image component

### AC 9.3: Code Quality
- [ ] Laravel Pint or PHP CS Fixer configured
- [ ] ESLint + Prettier for Vue files
- [ ] No console errors in browser
- [ ] Type safety with prop validation in Vue components
- [ ] Reusable components for common patterns

---

## 10. Environment Setup

### AC 10.1: Required Configuration
- [ ] `.env.example` with all required variables
- [ ] README.md with setup instructions
- [ ] Database migrations run cleanly
- [ ] `php artisan storage:link` documented
- [ ] Mailtrap credentials in .env.example

### AC 10.2: Commands to Run
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run dev
php artisan serve
```

---

## Bonus Features (Time Permitting)

- [ ] API documentation with Scribe or Postman collection
- [ ] Export projects/tasks to PDF
- [ ] Activity log with Spatie Activity Log
- [ ] Toast notifications in frontend with PrimeVue Toast
- [ ] Dark mode toggle

---

## Roadmap Features Coverage Checklist

### Routing Advanced
- [x] Route Model Binding in Resource Controllers
- [x] Route Redirect (homepage → login)

### Database Advanced
- [x] Database Seeders and Factories
- [x] Eloquent Query Scopes
- [x] Polymorphic relationships (Spatie Media Library)
- [x] Eloquent Accessors and Mutators (m/d/Y format)
- [x] Soft Deletes

### Auth Advanced
- [x] Authorization: Roles/Permissions, Gates, Policies (Spatie Permissions)
- [x] Authentication: Email Verification

### API Basics
- [x] API Routes and Controllers
- [x] API Eloquent Resources
- [x] API Auth with Sanctum
- [x] Override API Error Handling and Status Codes

### Debugging Errors
- [x] Try-Catch and Laravel Exceptions
- [x] Customizing Error Pages

### Sending Email
- [x] Mailables and Mail Facade
- [x] Notifications System: Email

### Extra
- [x] Automated Tests for CRUD Operations

---

**Notes**:
- This AC list follows TDD principles - write tests first, then implement features
- Sanctum cookie-based authentication is recommended for Inertia SPAs
- Focus on backend testing (Feature + Unit), frontend testing is bonus
- Use factories extensively in tests for clean, maintainable test code
- PrimeVue components enhance UX but core functionality should work without JavaScript

# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project overview

This is a multi-tenant Point of Sale (POS) web application built with plain PHP and MySQL (via `mysqli`). It is designed to run under a traditional PHP web server (e.g. XAMPP/Apache) rather than a framework or build system.

Key characteristics:
- Single database (`multi-pos`) shared by all tenants.
- Multi-tenancy implemented via a `tenant_id` column on business tables and helper functions in `config/function.php`.
- Role-based dashboards and CRUD UIs separated into three folders: `admin/`, `manager/`, and `salesman/`.

The root `README.md` currently contains only the project name; this file is the main source of guidance for Warp.

## Running the application locally

This repo assumes a classic PHP + MySQL stack, typically via XAMPP on Windows.

### Database connection

The global database connection is configured in `config/dbcon.php`:
- Host: `localhost`
- Port: `3307`
- User: `root`
- Database: `multi-pos`

Every request that includes `config/function.php` (and thus `dbcon.php`) will create a `mysqli` connection and currently echoes `Connected successfully!` on successful connection. Be aware that this output is sent on every request; if you are asked to clean up or harden the app, this echo is a likely target for removal or gating.

### Multi-tenant migration

Schema changes for multi-tenancy are handled by `database/migrate.php`.

This script:
- Ensures a `tenants` table exists.
- Adds a `tenant_id` column and index to `admins` and `customers` if they do not already exist.
- Creates a `tenant_settings` table.
- Inserts a default tenant with `tenant_id = 'default'` if missing.
- Backfills `tenant_id = 'default'` for existing `admins` and `customers` where `tenant_id` is empty/NULL.

You can run this migration either:
- Via browser, after the app is served: `http://localhost/multiPOS/database/migrate.php`, or
- Via CLI from the project root:

  ```bash path=null start=null
  php database/migrate.php
  ```

Use this script when you are modifying tenant-related behavior or after setting up a fresh database that already has the base POS tables but not the multi-tenant extensions.

### Serving the app

There is no build step; PHP files are served directly.

Typical local setup (XAMPP-style):
- Place this repository under the web root (e.g. `htdocs/multiPOS`).
- Ensure Apache and MySQL are running.
- Create/import the `multi-pos` database schema, then run the migration script as described above.
- Access the app in a browser at `http://localhost/multiPOS/`.

Important entry points:
- Public landing: `index.php` (root)
- Tenant registration: `tenant-register.php` (+ `tenant-register-code.php` for processing)
- Login: `login.php` (+ `login-code.php` for processing)
- Role dashboards (after login):
  - Admin: `admin/index.php`
  - Manager: `manager/index.php`
  - Salesman: `salesman/index.php`

### Linting and tests

This repository does **not** define any automated linting or test tooling (no `composer.json`, no PHPUnit config, etc.).

Implications for Warp:
- Do not assume a test runner exists; there is no standard command to "run a single test" because there is no test suite.
- If asked to add tests or linting, you will first need to introduce an appropriate PHP toolchain (e.g. PHPUnit, Pest, PHP_CodeSniffer) and corresponding configuration/commands.

## High-level architecture

### Global configuration and helpers

- `config/dbcon.php` – Creates the global `$conn` (`mysqli`) connection used throughout the app.
- `config/function.php` – Centralizes most shared behavior:
  - Session startup.
  - Input sanitization (`validate`).
  - Redirects with flash/status messages (`redirect`, `alertMessage`).
  - Generic CRUD helpers (`insert`, `update`, `getAll`, `getById`, `delete`, `getCount`).
  - Session/auth helpers (`logoutSession`, `jsonResponse`, `checkParamId`).
  - Multi-tenant helpers:
    - `getTenantInfo`, `getCompanyName`, `getCompanyDisplayInfo` – Fetch tenant metadata for the current or specified `tenant_id`.
    - `addTenantFilter`, `addTenantFilterToQuery`, `executeTenantQuery` – Utilities that attach `tenant_id` filters to queries, defaulting to the `tenant_id` from `$_SESSION['loggedInUser']` when not explicitly provided.

Most pages in `admin/`, `manager/`, and `salesman/` include `config/function.php` (directly or indirectly via their own `includes/header.php`), so these helpers are the right place to centralize new cross-cutting logic for validation, tenant scoping, etc.

### Multi-tenant model

Multi-tenancy is implemented at the database/query layer:
- Every row relevant to a tenant (e.g. `admins`, `customers`, `categories`, `products`, `orders`, `expenses`) has or is expected to have a `tenant_id` column.
- The currently active tenant is determined from the logged-in user: `$_SESSION['loggedInUser']['tenant_id']`.
- Helper functions in `config/function.php` either:
  - Automatically derive `tenant_id` from the session when not passed explicitly, or
  - Provide utilities to append `tenant_id = ...` conditions to raw SQL queries.

In newer or refactored code, prefer to use these helpers rather than manually concatenating `tenant_id` into queries, to keep tenant scoping consistent and easier to audit.

### Directory and role structure

The application is organized by user role, with substantial duplication across role-specific directories:

- `admin/`
- `manager/`
- `salesman/`

Each of these role directories typically contains:
- `index.php` – Role-specific dashboard summarizing metrics (customer count, category count, product count, admin count, sales totals, expense totals) filtered by the current tenant.
- `includes/` – Header/navbar/sidebar/footer/layout files for that role's dashboard views.
- `assets/` – JS bundles (Bootstrap, jQuery) and feature scripts for charts and DataTables.
- CRUD pages:
  - `*-create.php`, `*-edit.php`, `*-delete.php`, plus listing pages like `customers.php`, `categories.php`, `products.php`, `orders.php`, etc.
- Action handlers:
  - Consolidated form-handling files such as `code.php`, `orders-code.php`, `process-order.php` that read POST/GET data, call helpers from `config/function.php`, and redirect with status messages.

The three role directories mostly mirror each other, differing more in access level/available menu items than in core business logic. When adding new entity types or screens, you may need to update multiple role directories for consistency.

### Public pages and shared includes

Outside the role-specific directories:
- Root-level `index.php` – Landing/marketing page that shows either call-to-action buttons (tenant registration, login) or a dashboard/login/logout prompt depending on `$_SESSION['loggedIn']`.
- `tenant-register.php` / `tenant-register-code.php` – Public registration flow to create a new tenant and associated user.
- `login.php` / `login-code.php` – Handles authentication and populates `$_SESSION['loggedIn']` and `$_SESSION['loggedInUser']` (which includes `tenant_id` and likely a role indicator).
- `logout.php` – Clears session and logs the user out.
- `includes/` (root) – Shared header/footer/navbar for the public-facing pages.

Authentication and tenant selection are therefore tightly coupled to session state, and the rest of the app assumes that `$_SESSION['loggedInUser']` is present and valid.

## Notes for future Warp agents

- When working on business logic that should be tenant-aware, look first at `config/function.php` and the existing multi-tenant helpers before introducing new patterns.
- The migration script (`database/migrate.php`) is the authoritative place where tenant-related schema changes are codified; keep it in sync with any new `tenant_id` columns or tenant-specific tables you introduce.
- There is no existing automated test suite or composer-based dependency management; if asked to add significant new functionality, consider whether the task includes introducing those tools before relying on them in commands or examples.

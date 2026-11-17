# NusaBoard

NusaBoard is a full Laravel 13 community publishing application used as a local web-security learning environment. It has real routes, controllers, Eloquent models, migrations, authentication, authorization concepts, Blade views, and seeded data.

> **Training-only warning:** several features intentionally contain vulnerable code. Keep this app on your own machine, use fictional data, and never deploy it publicly or expose it to a network.

## Stack

- Laravel 13
- PHP 8.3+
- MariaDB / MySQL (default) or SQLite after configuring the connection
- Blade templates
- Plain CSS (no frontend build step)

## Features

- Registration, login, logout, and session handling
- Member dashboard with content statistics
- Article create, edit, publish, feature, and delete workflows
- Public article detail pages and comments
- Search and category browsing
- Admin overview and user directory
- Seed accounts, categories, published articles, drafts, and comments
- Security-lab notes beside deliberately vulnerable features

## Requirements

- PHP 8.3 or newer
- Composer 2
- MariaDB / MySQL with the PHP \`pdo_mysql\` extension
- PHP extensions required by Laravel, including \`mbstring\`, \`openssl\`, \`pdo\`, \`tokenizer\`, \`xml\`, \`ctype\`, \`curl\`, \`fileinfo\`, and \`json\`

## Install on a local machine

From this directory:

\`\`\`bash
cp .env.example .env
\`\`\`

Create the local database:

\`\`\`sql
CREATE DATABASE nusaboard_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
\`\`\`

Update \`DB_USERNAME\` and \`DB_PASSWORD\` in \`.env\` for your local MariaDB account, then run:

\`\`\`bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --host=127.0.0.1 --port=8000
\`\`\`

Open **http://127.0.0.1:8000**.

No Node.js or npm build is required. The app uses a local stylesheet.

## Demo accounts

All seeded accounts use the local-only password \`password\`.

| Role | Email |
|---|---|
| Admin | \`admin@nusaboard.test\` |
| Editor | \`editor@nusaboard.test\` |
| Member | \`member@nusaboard.test\` |

Registration creates a member account.

## Security exercises

The vulnerable behavior is intentionally implemented in normal application flows, not in a separate toy page.

| Exercise | Where to inspect | What to learn |
|---|---|---|
| SQL injection | \`HomeController.php\` public search | Raw interpolation vs parameter binding |
| Stored XSS | \`CommentController.php\` + \`articles/show.blade.php\` | Stored input and escaped output |
| IDOR / BOLA | \`ArticleController.php\` edit, update, delete | Ownership checks and policies |
| Mass assignment | \`Article.php\` + \`ArticleController.php\` | Explicit validated fields and guarded model attributes |
| Role authorization | \`AdminController.php\` + \`/admin\` route | Authentication vs authorization |

Each page includes a lab note. Source comments marked \`VULNERABLE LAB\` identify intentionally unsafe lines.

### Suggested learning flow

1. Register a member account and inspect the dashboard.
2. Search the public article list. Inspect the raw SQL construction in the home controller.
3. Post a normal comment, then inspect the comment rendering path and compare it with normal escaped Blade output.
4. Create an article and review the controller's request handling and model fillable fields.
5. Open your own article edit screen, then inspect how the route resolves other article IDs and what authorization check is missing.
6. Open \`/admin\` directly while signed in as a member. Compare navigation visibility with server-side route middleware.
7. For each issue, write down the root cause, impact, and a secure Laravel fix before changing the code.

## Running tests

\`\`\`bash
php artisan test
\`\`\`

The included smoke tests cover the login page, registration page, and guest dashboard redirect.

## Restore seed data

The following command drops and recreates the local tables, then restores fictional demo data:

\`\`\`bash
php artisan migrate:fresh --seed
\`\`\`

This deletes changes made to the local training database.

## Production warning

Do not use this code as a production starter. Remove the intentionally vulnerable search, output rendering, mass assignment, record update/delete, and admin access patterns before deployment. Add authorization policies, validated allowlists, parameterized queries, escaped output, and security regression tests.

# Running on Replit

This is the imported Laravel 13 CRM application, using PHP 8.4, Composer,
Node.js 22, Vite 8, and its original SQLite database configuration.
The existing application structure is unchanged.
Laravel trusts forwarded protocol and port headers from the hosting proxy
so asset URLs and redirects stay HTTPS in the preview. Forwarded host headers
are not trusted.

## Start

Click **Run** to start the **Start application** workflow. It builds the frontend
assets and serves Laravel on `0.0.0.0:5000`. The root URL redirects to `/crm`.
The initial database is empty; no demo users or business records are seeded.

## First-time setup

1. Install PHP dependencies: `composer install --no-interaction --prefer-dist`.
2. Install frontend dependencies: `npm ci`.
3. Copy `.env.example` to `.env` only if `.env` does not already exist.
4. Generate Laravel's local encryption key: `php artisan key:generate --no-interaction`.
   Never commit `.env` or expose the key.
5. Create `database/database.sqlite` if missing, then run
   `php artisan migrate --no-interaction`.
6. Click **Run**.

SQLite data is kept in `database/database.sqlite`, excluded from Git. Do not
delete it or use `migrate:fresh` on data that must be retained.
Background queue records use `queue_jobs`; the separate `jobs` table stores
CRM work orders. No queue worker is started by this web workflow.

## Checks

- `npm run build`
- `php artisan test --compact`
- `php artisan migrate:status`

## Environment and limitations

- Development settings use `APP_ENV=local`, `APP_DEBUG=true`, and `DB_CONNECTION=sqlite`.
- `MAIL_MAILER=log` writes mail to Laravel's log; real email delivery requires
  a separately configured mail provider.
- The imported CRM routes currently have no sign-in protection. This setup is
  for development, not a production release; do not enter sensitive data or
  publish it publicly until access control is added.
- `startup.sh` and `nginx.conf` are legacy Azure/Debian deployment files and
  are not used by the Replit workflow.
- The repository requires Laravel Boost for agent development; it is installed
  as a development dependency and its generated guidance is in `CLAUDE.md`.

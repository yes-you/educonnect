# Deploying EduConnect: Supabase (database) + Railway (PHP host)

Supabase only provides Postgres here. It cannot run Laravel, so Railway runs the app.
Local development stays on XAMPP/MySQL. Only the production `.env` points at Supabase.

## 1. Supabase (database)

1. Create a project at supabase.com. Save the database password.
2. Project > Connect > **Session pooler** (port 5432). Railway is IPv4-only, so do not use the direct
   connection. Do not use the Transaction pooler (port 6543): it breaks Laravel's prepared statements.
3. Note these values: host (`aws-0-<region>.pooler.supabase.com`), port `5432`,
   database `postgres`, user `postgres.<project-ref>`, and your password.

## 2. Railway (app)

1. Push the repo to GitHub (never commit `.env`).
2. Railway > New Project > Deploy from GitHub repo. It detects Laravel and runs `composer install`
   and `npm run build`.
3. Service > Variables:

```
APP_NAME=EduConnect
APP_ENV=production
APP_DEBUG=false
APP_KEY=            # run: php artisan key:generate --show   (paste the output)
APP_URL=https://<your-service>.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=aws-0-<region>.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<supabase db password>
DB_SSLMODE=require

TRUSTED_PROXIES=*   # Railway's proxy addresses change, so trust it; keep unset anywhere else
ADMIN_PASSWORD=     # at least 12 characters, used once by the seeder
ADMIN_EMAIL=        # optional, defaults to admin@educonnect.test

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stderr
```

4. Service > Settings > **Pre-deploy command**: `php artisan migrate --force`
5. Service > Settings > **Custom start command** (only if the default does not serve `public/`):
   `php artisan storage:link && php -S 0.0.0.0:$PORT -t public`
6. Service > Volumes > add a volume mounted at `/app/storage/app/public`. Without it, uploaded
   module files are deleted on every redeploy.
7. Settings > Networking > Generate domain.

## 3. Create the admin account (once)

`php artisan db:seed --force` from the Railway shell (or `railway run php artisan db:seed --force`).
With `APP_ENV=production` the seeder creates only one admin, using `ADMIN_PASSWORD` (and `ADMIN_EMAIL`), and
no demo accounts or demo content. It stops with an error if `ADMIN_PASSWORD` is missing or shorter than 12 characters.
Remove `ADMIN_PASSWORD` from the variables after seeding.

## Known differences from MySQL

- `LIKE` search (opportunity title) is case-sensitive on Postgres. MySQL ignored case.
- `->after('column')` in migrations is ignored on Postgres. Harmless: column order only.
- The app was tested on MySQL and SQLite only. Run through the six seeded roles once after deploy.

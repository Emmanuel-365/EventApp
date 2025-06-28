<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Docker Development Environment

This project includes a Docker-based development environment.

### Prerequisites

*   Docker Desktop (or Docker Engine + Docker Compose) installed.

### Setup

1.  **Clone the repository:**
    ```bash
    git clone <your-repo-url>
    cd <your-repo-directory>
    ```

2.  **Create your local environment file:**
    *   Copy `.env.docker-example` to `.env`:
        ```bash
        cp .env.docker-example .env
        ```
    *   **Important:** Open the `.env` file and:
        *   Generate an application key: Run `docker-compose run --rm app php artisan key:generate` (once containers are built and running, or generate it locally if you have PHP and copy it in) and paste the key into `APP_KEY=`.
        *   Review other variables, especially `APP_URL`, `DB_PASSWORD` (must match `MYSQL_ROOT_PASSWORD`), and port mappings if needed.
        *   For multi-tenancy, ensure `DB_USERNAME=root` and `DB_PASSWORD` matches `MYSQL_ROOT_PASSWORD` for easy tenant database creation permissions during local development.

3.  **Build and start the Docker containers:**
    ```bash
    docker-compose build
    docker-compose up -d
    ```
    The first build might take some time as it downloads images and installs dependencies.

4.  **Install Composer dependencies (if not handled by entrypoint or if you need to update):**
    While the Dockerfile runs `composer install`, if you encounter issues or need to update after a `git pull`, you can run:
    ```bash
    docker-compose exec app composer install
    ```

5.  **Database Migrations:**
    *   The entrypoint script (`docker-entrypoint.sh`) attempts to run central migrations automatically.
    *   It also attempts to run tenant setup/migrations based on `.env` variables (`SETUP_TENANTS_ON_STARTUP`, `AUTO_MIGRATE_TENANTS`).
    *   To run migrations manually:
        ```bash
        docker-compose exec app php artisan migrate # Central migrations
        docker-compose exec app php artisan tenants:setup # If you want to run the custom setup for all tenants
        # OR
        docker-compose exec app php artisan tenants:migrate # For existing tenant migrations
        ```

6.  **Accessing the Application:**
    *   **Web:** Open your browser and go to `http://localhost` (or the `APP_URL` and `APP_PORT` you configured in `.env`).
    *   **Mailpit (Email Catchall):** `http://localhost:8025` (or `FORWARD_MAILPIT_UI_PORT`).
    *   **Database (e.g., via TablePlus, DataGrip):**
        *   Host: `127.0.0.1`
        *   Port: `3306` (or `FORWARD_DB_PORT`)
        *   User: `root` (or `DB_USERNAME` from `.env`)
        *   Password: `secret` (or `DB_PASSWORD` from `.env`)
        *   Database: `laravel_central` (or `DB_DATABASE` from `.env`)

7.  **Multi-Tenancy Setup (Local Development):**
    *   Ensure your `.env` file has `DB_USERNAME=root` and `DB_PASSWORD` matching `MYSQL_ROOT_PASSWORD` (e.g., `secret`). This gives the application rights to create tenant databases.
    *   Set `SETUP_TENANTS_ON_STARTUP=true` in `.env` to run the `php artisan tenants:setup` command when containers start. This command should create databases, migrate, and seed for tenants found in your central `organizations` table.
    *   **Tenant Domains:** To access tenant subdomains (e.g., `tenant1.localhost`, `foo.localhost`):
        *   You need to add them to your local `/etc/hosts` file (macOS/Linux) or `C:\Windows\System32\drivers\etc\hosts` (Windows).
        *   Example:
            ```
            127.0.0.1 localhost
            127.0.0.1 tenant1.localhost
            127.0.0.1 foo.localhost
            ```
        *   Ensure `APP_URL` in `.env` is `http://localhost` and `CENTRAL_DOMAIN` is `localhost`. When you create a tenant with a domain like `foo`, it will be accessible as `http://foo.localhost`.

### Common Docker Commands

*   **Start containers in detached mode:** `docker-compose up -d`
*   **Stop containers:** `docker-compose down`
*   **View logs for all services:** `docker-compose logs -f`
*   **View logs for a specific service:** `docker-compose logs -f app`
*   **Execute a command in a running container (e.g., Artisan):**
    ```bash
    docker-compose exec app php artisan <your-command>
    docker-compose exec app bash # To get a shell inside the app container
    ```
*   **Rebuild containers (e.g., after Dockerfile changes):** `docker-compose build` or `docker-compose up -d --build`
*   **List running containers:** `docker-compose ps`

### Notes

*   **Frontend Assets:** The `Dockerfile` builds frontend assets using `yarn build`. If you need to develop frontend assets with Hot Module Replacement (HMR):
    1.  Modify the `app` service in `docker-compose.yml`:
        *   Change `command` to something like: `bash -c "yarn install && yarn dev --host 0.0.0.0"`
        *   Add/uncomment port mapping for Vite: `ports: - "5173:5173"`
    2.  Update your `.env` file:
        *   Comment out or remove `VITE_ASSET_URL`.
        *   Set `VITE_ORIGIN_URL="http://localhost:5173"` (or the host/port Vite uses).
    3.  Ensure your `vite.config.js` is configured for HMR (e.g., `server: { host: '0.0.0.0', port: 5173, hmr: { host: 'localhost' } }`).
*   **Xdebug:** Xdebug is not configured in this setup. To add it, you would need to modify the `Dockerfile` to install the extension and configure PHP and your IDE for debugging.
*   **Permissions:** If you encounter permission issues with `storage` or `bootstrap/cache` directories, ensure they are writable by the `www-data` user inside the container. The Dockerfile attempts to set this up. You might need to run `sudo chown -R $USER:www-data storage bootstrap/cache` on your host and then `sudo chmod -R 775 storage bootstrap/cache` if issues persist due to host volume mounting.
```

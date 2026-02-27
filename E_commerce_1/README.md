# Simple PHP E‑commerce (scaffold)

This is a minimal PHP + MySQL e‑commerce scaffold (product listing, cart, checkout mock, admin).

Quick start (Docker)
1. Copy `.env.example` to `.env` and edit DB credentials if needed.
2. Run: `docker-compose up --build -d`
3. Initialize DB (one-time):
   - `docker exec -i bosse_mysql mysql -u root -p${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} < init.sql`
   - Or use Adminer/phpMyAdmin to import `init.sql`.
4. Browse app at http://localhost:8080

Git
- Branch name intended: `ecommerce-php`

Default admin login (seeded in init.sql)
- admin@example.com / password: admin123

Notes
- This scaffold uses PDO prepared statements.
- Checkout is a mock (no payment gateway); can be extended to Stripe upon request.
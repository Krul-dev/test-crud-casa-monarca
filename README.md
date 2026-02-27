# test-crud-casa-monarca

Simple testing repository for a CRUD app.

## PHP CRUD (Apache + MariaDB)

The minimal CRUD app is in the repository root:
- `index.php`: create/read/update/delete users
- `db.php`: database connection via environment variables
- `schema.sql`: database and table creation

### 1) Create database/table and app user
```bash
sudo mariadb < schema.sql
```

### 2) Configure database credentials
The app reads:
- `DB_HOST` (default: `localhost`)
- `DB_PORT` (default: `3306`)
- `DB_NAME` (default: `test_crud`)
- `DB_USER` (default: `crud_user`)
- `DB_PASS` (default: `crud_pass`)

Set them in Apache virtual host/env or in your shell before starting PHP.

### 3) Open in browser
Serve the repository from Apache and visit:
`http://localhost/test-crud-casa-monarca/`

## Session Dev Helpers

Use these helper scripts to bind this repository into `/srv/http/test-crud-casa-monarca` for one session:

```bash
./dev-on
```

This enables the bind mount and starts `httpd`.

When done:

```bash
./dev-off
```

This unmounts the bind mount (if active) and stops `httpd`.

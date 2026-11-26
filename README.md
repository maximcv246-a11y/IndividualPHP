# Movie Catalog

Simple PHP + SQLite student web application.

## Requirements
- PHP 8 or newer
- PDO SQLite extension enabled

## Run locally
1. Extract the project folder.
2. Open a terminal in the project folder.
3. Start the PHP development server:

   ```bash
   php -S localhost:8000
   ```

4. Open http://localhost:8000 in your browser.
5. The SQLite database file is created automatically in `database/movie_catalog.sqlite` on first visit, and sample movies are inserted.

## Create the first administrator
For security, the project does not include a default administrator password. Register an ordinary account first. Then, using SQLite (for example, DB Browser for SQLite), run:

```sql
UPDATE users SET role = 'admin' WHERE username = 'YOUR_USERNAME';
```

Log out and log back in to refresh your session role. The Admin panel will appear in the navigation.

## Features
- Public movie catalog loaded from SQLite
- Search by title, genre, and year
- Registration and login
- Password hashing and verification
- Session-based access protection
- User movie submission with client and server validation
- Admin movie management: list, add, edit, delete
- Create additional administrator accounts
- Prepared SQL statements, HTML escaping and CSRF tokens

## Database
SQLite database is created automatically by `config/database.php`. Tables: `users` and `movies`.

## Project structure
- `config/` database connection and schema initialization
- `includes/` common functions and header/footer
- Individual PHP pages for public, user, and admin functionality
- `css/` intentionally minimal styles
- `database/` generated SQLite database file

# ShelfDB

ShelfDB is a simple, self-hosted web application for managing a product database using SQLite3. It provides a web interface to add, view, and search products, making it ideal for small inventory or catalog management needs.

---

## Features

- Add new products with name, department, price, quantity, and UPC
- View all products in a sortable table
- Search for products by UPC
- User authentication and permissions
- Simple, clean web interface
- Uses SQLite3 for easy, file-based storage

---

## Requirements

- PHP 7.4+ with SQLite3 extension enabled
- A web server (e.g., Apache, Nginx, or PHP's built-in server)
- Python 3 (for initial setup script)

---

## Getting Started

1. **Clone the repository:**
    ```sh
    git clone https://github.com/AceCJM/ShelfDB.git
    cd ShelfDB/src
    ```

2. **Set up the database and admin user:**
    ```sh
    python3 setup.py
    ```
    - This script will create the database, tables, and a default admin user.
    - You can specify the database location and admin credentials during setup.

3. **Start the PHP built-in server (for development):**
    ```sh
    php -S localhost:8080
    ```
    Then open [http://localhost:8080](http://localhost:8080) in your browser.

---

## Directory Structure

```
src/
  ├── addProduct.php
  ├── allProducts.php
  ├── db/
  │    ├── Database.php
  │    ├── User.php
  │    ├── UserAuth.php
  │    └── UserPermissions.php
  ├── css/
  │    └── style.css
  ├── index.php
  ├── login.php
  ├── searchProduct.php
  ├── setup.py
  └── management/
       └── login.php
```

---

## Usage

- **Home:** Overview and navigation.
- **All Products:** View all products in the database.
- **Add Product:** Add a new product to the database.
- **Search Product:** Search for a product by UPC.
- **Login:** Authenticate as a user to access features.
- **User Management:** (If enabled) Manage users and permissions via `management/login.php`.

---

## User Management & Permissions

- Users are stored in the `users` table.
- Permissions are managed in the `user_permissions` table.
- The default admin user is created during setup and has full permissions.
- You can extend user and permission management in `db/User.php`, `db/UserAuth.php`, and `db/UserPermissions.php`.
- The `UserPermissions` class checks permissions for actions like managing users.

---

## Configuration

- To use a custom database file, set the `DB_FILE` environment variable:
    ```sh
    export DB_FILE=/path/to/your/database.db
    ```
- The PHP executable path can be set in your `.env` file as `PHP_EXECUTABLE`.

---

## Customization

- Edit `src/css/style.css` to change the look and feel.
- Extend `src/db/Database.php` and `AppDatabase` for more advanced features.
- Add or modify permission logic in `db/UserPermissions.php`.

---

## Security Notes

- Passwords are hashed using PHP's `password_hash` and verified with `password_verify`.
- Do not expose your database file or `.env` file to the public.

---

## License

MIT License

---

*Self-hosted product and user management for small teams

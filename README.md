# ShelfDB

ShelfDB is a simple, self-hosted web application for managing a product database using SQLite3. It provides a web interface to add, view, and search products, making it ideal for small inventory or catalog management needs.

## Features

- Add new products with name, department, price, and UPC
- View all products in a sortable table
- Search for products by UPC
- Simple, clean web interface
- Uses SQLite3 for easy, file-based storage

## Requirements

- PHP 7.4+ with SQLite3 extension enabled
- A web server (e.g., Apache, Nginx, or PHP's built-in server)

## Getting Started

1. **Clone the repository:**
    ```sh
    git clone https://github.com/yourusername/ShelfDB.git
    cd ShelfDB/src
    ```

2. **Set up the database:**
    - By default, the database file will be created at `db/shelf.db` on first run.
    - You can specify a custom location by setting the `DB_FILE` environment variable.

3. **Start the PHP built-in server (for development):**
    ```sh
    php -S localhost:8080
    ```
    Then open [http://localhost:8080](http://localhost:8080) in your browser.

4. **Directory Structure:**
    ```
    src/
      ├── addProduct.php
      ├── allProducts.php
      ├── db/
      │    └── Database.php
      ├── css/
      │    └── style.css
      ├── index.php
      └── searchProduct.php
    ```

## Usage

- **Home:** Overview and navigation.
- **All Products:** View all products in the database.
- **Add Product:** Add a new product to the database.
- **Search Product:** Search for a product by UPC.

## Configuration

- To use a custom database file, set the `DB_FILE` environment variable:
    ```sh
    export DB_FILE=/path/to/your/database.db
    ```

## Customization

- Edit `src/css/style.css` to change the look and feel.
- Extend `src/db/Database.php` and `AppDatabase` for more advanced features.

## License

MIT License

---

*Self Hosted

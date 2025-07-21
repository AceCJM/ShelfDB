# This file is part of ShelfDB, a personal database management system.
# Setup script for the ShelfDB project.
import sqlite3, dotenv, subprocess, os

# Get PHP executable path
dotenv.load_dotenv()
if not os.environ.get("PHP_EXECUTABLE"):
    print("PHP_EXECUTABLE not found in .env file.")
    php_executable = input("Enter the path to the PHP executable (default: php): ") or "php"
    dotenv.set_key(".env", "PHP_EXECUTABLE", php_executable)
else:
    php_executable = os.environ["PHP_EXECUTABLE"]


db_file = input("Enter the path to the database file (default: db/shelf.db): ") or "db/shelf.db"
try:
    conn = sqlite3.connect(db_file)
    cursor = conn.cursor()

    # Create tables if they do not exist
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL
        )
    ''')

    cursor.execute('''
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            upc INTEGER NOT NULL UNIQUE,
            name TEXT NOT NULL,
            department TEXT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 0,
            price REAL NOT NULL
        )
    ''')

    cursor.execute('''
        CREATE TABLE IF NOT EXISTS user_permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id TEXT NOT NULL,
            permission TEXT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ''')

    conn.commit()
    print("Database setup completed successfully.")

    # Insert default admin user
    admin_username = input("Enter admin username (default: admin): ") or "admin"
    admin_password = input("Enter admin password (default: admin_password): ") or "admin_password"
    # Create a hashed password for the admin user
    output = subprocess.run([f"{php_executable}", "-r", f"echo password_hash('{admin_password}', PASSWORD_DEFAULT);"], capture_output=True, text=True)
    admin_password_hash = output.stdout.strip()
    # Insert the admin user into the database
    cursor.execute('''
        INSERT OR IGNORE INTO users (user_id, password_hash, role)
        VALUES (?, ?, 'administrator')
    ''', (admin_username, admin_password_hash))
    conn.commit()
    print(f"Admin user '{admin_username}' created successfully.")
    print(f"Admin password hash: {admin_password_hash}")
    # Add manager_user permission to the admin user
    cursor.execute('''
        INSERT OR IGNORE INTO user_permissions (user_id, permission)
        VALUES (?, 'manage_users')
    ''', (admin_username,))
    conn.commit()
    print(f"Permission 'manage_users' granted to admin user '{admin_username}'.")

except sqlite3.Error as e:
    print(f"An error occurred: {e}")
finally:
    if conn:
        conn.close()
        print("Database connection closed.")
# End of setup script
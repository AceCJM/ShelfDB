<?php
// src/db/database.php

/*
 * This file defines the Database class for interacting with an SQLite3 database.
 * It provides methods to connect, query, fetch, insert, and close the database connection.
 */

class Database
{
    private SQLite3 $db; // SQLite3 database connection object

    // Constructor: Opens a connection to the SQLite3 database file
    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
    }

    // Executes a SQL query and returns the result object
    /**
     * @throws Exception
     */
    public function query($request, $params = []): SQLite3Result
    {
        $stmt = $this->db->prepare($request); // Prepare the SQL statement
        if (! $stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        // Bind parameters if provided
        if (! empty($params)) {
            $i = 1;
            foreach ($params as $value) {
                $type = is_int($value) ? SQLITE3_INTEGER : (is_float($value) ? SQLITE3_FLOAT : SQLITE3_TEXT);
                $stmt->bindValue($i, $value, $type);
                $i++;
            }
        }
        $result = $stmt->execute(); // Execute the statement
        if (! $result) {
            throw new Exception("Query failed: " . $this->db->lastErrorMsg());
        }
        return $result; // Return the result object
    }

    // Executes a SQL query and returns all results as an array of associative arrays

    // Inserts a new row into the specified table using an associative array of data

    /**
     * @throws Exception
     */
    public function insert($table, $data): void
    {
        $columns      = implode(", ", array_keys($data));                // Get column names
        $placeholders = implode(", ", array_fill(0, count($data), '?')); // Create placeholders for values
        $sql          = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->prepareSQL($sql, $data); // Prepare the SQL statement
        $i = 1;
        // Bind each value to its placeholder, using the correct SQLite type
        foreach ($data as $value) {
            $stmt->bindValue($i, $value, is_int($value) ? SQLITE3_INTEGER : (is_float($value) ? SQLITE3_FLOAT : SQLITE3_TEXT));
            $i++;
        }
        // Execute the insert statement
        if (! $stmt->execute()) {
            throw new Exception("Insert failed: " . $this->db->lastErrorMsg());
        }
    }

    /**
     * @throws Exception
     */
    public function update($table, $data, $where): void
    {
        // Prepare the SET clause with placeholders
        $setClause = implode(", ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));
        // Prepare the SQL statement
        $sql  = "UPDATE $table SET $setClause WHERE $where";
        $stmt = $this->prepareSQL($sql, $data);
        // Execute the update statement
        if (! $stmt->execute()) {
            throw new Exception("Update failed: " . $this->db->lastErrorMsg());
        }

    }

    // Closes the database connection
    public function close(): void
    {
        $this->db->close();
    }

    /**
     * @param string $sql
     * @param $data
     * @return SQLite3Stmt
     * @throws Exception
     */
    public function prepareSQL(string $sql, $data): SQLite3Stmt
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        // Bind each value to its placeholder
        $i = 1;
        foreach ($data as $value) {
            $stmt->bindValue($i, $value, is_int($value) ? SQLITE3_INTEGER : (is_float($value) ? SQLITE3_FLOAT : SQLITE3_TEXT));
            $i++;
        }
        return $stmt;
    }
}

// Class to contain application-specific database logic
class AppDatabase
{
    private Database $db;

    /**
     * @throws Exception
     */
    public function __construct($dbFile)
    {
        // Call the parent constructor with the database file path
        $this->db = new Database($dbFile);
    }

    /**
     * @throws Exception
     */
    public function queryUPC($upc): array
    {
        $result = $this->db->query('SELECT * FROM PRODUCTS WHERE upc = ? ', [$upc]);
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }

    /**
     * @throws Exception
     */
    public function fetchAllProducts(): array
    {
        // Fetch all products from the database
        $result = $this->db->query("SELECT * FROM products");
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }

    /**
     * @throws Exception
     */
    public function updateProduct($upc, $data): void
    {
        // Update the product with the given UPC using the provided data
        $this->db->update('products', $data, "upc = '$upc'");
    }

    /**
     * @throws Exception
     */
    public function insertProduct($data): void
    {
        // Insert a new product into the products table
        $this->db->insert('products', $data);
    }

    /**
     * @throws Exception
     */
    public function getZeroExportData(): array
    {
        // Fetch products with zero quantity for export
        $result = $this->db->query("SELECT upc, name, price, department, quantity FROM products WHERE quantity = 0");
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }

    public function close(): void
    {
        // Close the database connection
        $this->db->close();
    }

    /**
     * @throws Exception
     */
    public function deleteProduct($productId): void
    {
        // Delete a product by its ID
        $this->db->query("DELETE FROM products WHERE id = ?", [$productId]);
    }
}

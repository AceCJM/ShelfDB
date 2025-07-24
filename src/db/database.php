<?php
// src/db/database.php

/*
 * This file defines the Database class for interacting with an SQLite3 database.
 * It provides methods to connect, query, fetch, insert, and close the database connection.
 */

class Database
{
    private $db; // SQLite3 database connection object

    // Constructor: Opens a connection to the SQLite3 database file
    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
        if (! $this->db) {
            throw new Exception("Could not connect to the database.");
        }
    }
    public function createTable($tableName, $columns)
    {
        // Create a SQL statement to create a table with the specified columns
        $stmt = $this->db->prepare("CREATE TABLE IF NOT EXISTS $tableName (" . implode(", ", $columns) . ")");
        if (! $stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        $result = $stmt->execute(); // Execute the prepared statement
        if (! $result) {
            throw new Exception("Failed to create table: " . $this->db->lastErrorMsg());
        }
    }
    // Executes a SQL query and returns the result object
    public function query($request, $params = [])
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
    public function fetch($sql)
    {
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        if (! $stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        $result = $stmt->execute(); // Execute the statement
        if (! $result) {
            throw new Exception("Query failed: " . $this->db->lastErrorMsg());
        }
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }

    // Inserts a new row into the specified table using an associative array of data
    public function insert($table, $data)
    {
        $columns      = implode(", ", array_keys($data));                // Get column names
        $placeholders = implode(", ", array_fill(0, count($data), '?')); // Create placeholders for values
        $sql          = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt         = $this->db->prepare($sql); // Prepare the SQL statement
        if (! $stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        $i = 1;
        // Bind each value to its placeholder, using the correct SQLite type
        foreach (array_values($data) as $value) {
            $stmt->bindValue($i, $value, is_int($value) ? SQLITE3_INTEGER : (is_float($value) ? SQLITE3_FLOAT : SQLITE3_TEXT));
            $i++;
        }
        // Execute the insert statement
        if (! $stmt->execute()) {
            throw new Exception("Insert failed: " . $this->db->lastErrorMsg());
        }
    }

    public function update($table, $data, $where)
    {
        // Prepare the SET clause with placeholders
        $setClause = implode(", ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));
        // Prepare the SQL statement
        $sql  = "UPDATE $table SET $setClause WHERE $where";
        $stmt = $this->db->prepare($sql);
        if (! $stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }
        // Bind each value to its placeholder
        $i = 1;
        foreach (array_values($data) as $value) {
            $stmt->bindValue($i, $value, is_int($value) ? SQLITE3_INTEGER : (is_float($value) ? SQLITE3_FLOAT : SQLITE3_TEXT));
            $i++;
        }
        // Execute the update statement
        if (! $stmt->execute()) {
            throw new Exception("Update failed: " . $this->db->lastErrorMsg());
        }

    }

    public function lastErrorMsg()
    {
        // Returns the last error message from the database connection
        return $this->db->lastErrorMsg();
    }

    // Closes the database connection
    public function close()
    {
        $this->db->close();
    }
}

// Class to contain application-specific database logic
class AppDatabase
{
    private $db;
    public function __construct($dbFile)
    {
        // Call the parent constructor with the database file path
        $this->db = new Database($dbFile);
    }
    public function queryUPC($upc)
    {
        $result = $this->db->query('SELECT * FROM PRODUCTS WHERE upc = ? ', [$upc]);
        if ($result === false) {
            throw new Exception("Query failed: " . $this->db->lastErrorMsg());
        }
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }
    public function fetchAllProducts()
    {
        // Fetch all products from the database
        $result = $this->db->query("SELECT * FROM products");
        if ($result === false) {
            throw new Exception("Query failed: " . $this->db->lastErrorMsg());
        }
        $data = [];
        // Fetch each row as an associative array and add to $data
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data; // Return all rows
    }
    public function updateProduct($upc, $data)
    {
        // Update the product with the given UPC using the provided data
        $this->db->update('products', $data, "upc = '$upc'");
    }
    public function insertProduct($data)
    {
        // Insert a new product into the products table
        $this->db->insert('products', $data);
    }
    public function close()
    {
        // Close the database connection
        $this->db->close();
    }
}

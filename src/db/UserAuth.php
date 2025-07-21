<?php
// src/db/UserPermissions.php
// This file defines the UserAuth class for handling user authentication.

class UserAuth
{
    private $db;

    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
        if (! $this->db) {
            throw new Exception("Could not connect to the database.");
        }
    }

    public function authenticate($userID, $password)
    {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $result = $stmt->execute();

        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            return password_verify($password, $row['password_hash']);
        }
        return false;
    }

    public function isAuthenticated()
    {
        // Check if the user is authenticated
        if (!isset($_SESSION['user_id'])) {
            return false; // User is not authenticated
        }
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false; // Return true if user exists
    }

    public function close()
    {
        $this->db->close();
    }
}

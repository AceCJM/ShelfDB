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
        // This could be implemented using session management or other methods
        return isset($_SESSION['user_id']);
    }

    public function close()
    {
        $this->db->close();
    }
}

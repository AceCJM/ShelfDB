<?php
// src/db/userPermissions.php
// This file defines the UserAuth class for handling user authentication.

class UserAuth
{
    private $db;

    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
    }

    public function authenticate($userID, $password): bool
    {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID);
        $result = $stmt->execute();

        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            return password_verify($password, $row['password_hash']);
        }
        return false;
    }

    public function updatePassword($userID, $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id");
        $stmt->bindValue(':password_hash', $passwordHash);
        $stmt->bindValue(':user_id', $userID);
        return $stmt->execute() !== false; // Return true if update was successful
    }

    public function isAuthenticated(): bool
    {
        // Check if the user is authenticated
        if (! isset($_SESSION['user_id'])) {
            return false; // User is not authenticated
        }
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false; // Return true if user exists
    }

    public function close()
    {
        $this->db->close();
    }
}

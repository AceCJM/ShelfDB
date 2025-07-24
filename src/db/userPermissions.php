<?php
// src/db/userPermissions.php
// This file defines the class for authenticating user permissions.

class UserPermissions
{
    private $db;

    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
        if (! $this->db) {
            throw new Exception("Could not connect to the database.");
        }
    }

    public function addPermission($userID, $permission)
    {
        $stmt = $this->db->prepare("INSERT INTO user_permissions (user_id, permission) VALUES (:user_id, :permission)");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->bindValue(':permission', $permission, SQLITE3_TEXT);
        return $stmt->execute() !== false;
    }

    public function checkPermission($userID, $permission)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_permissions WHERE user_id = :user_id AND permission = :permission");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->bindValue(':permission', $permission, SQLITE3_TEXT);
        $result = $stmt->execute();

        return $result && $result->fetchArray() !== false;
    }

    public function changePermission($userID, $permission)
    {
        $stmt = $this->db->prepare("UPDATE user_permissions SET permission = :permission WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->bindValue(':permission', $permission, SQLITE3_TEXT);
        return $stmt->execute();
    }
    public function getAvailablePermissions()
    {
        $result      = $this->db->query("SELECT DISTINCT permission FROM user_permissions");
        $permissions = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $permissions[] = $row['permission'];
        }
        return $permissions;
    }

    public function removePermission($userID, $permission)
    {
        $stmt = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = :user_id AND permission = :permission");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->bindValue(':permission', $permission, SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function addUser($userID, $userPassword, $permission = 'read')
    {
        // Insert a new user with default permission
        $stmt = $this->db->prepare("INSERT INTO users (user_id, password_hash) VALUES (:user_id, :password_hash)");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->bindValue(':password_hash', password_hash($userPassword, PASSWORD_DEFAULT), SQLITE3_TEXT);
        if (! $stmt->execute()) {
            throw new Exception("Failed to add user: " . $this->db->lastErrorMsg());
        }

        // Add default permission for the new user
        return $this->addPermission($userID, $permission);
    }

    public function deleteUser($userID)
    {
        // Delete user permissions first
        $stmt = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        $stmt->execute();
        // Then delete the user
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID, SQLITE3_TEXT);
        return $stmt->execute() !== false;
    }
    public function close()
    {
        $this->db->close();
    }
}

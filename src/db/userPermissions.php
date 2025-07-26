<?php
// src/db/userPermissions.php
// This file defines the class for authenticating user permissions.

class UserPermissions
{
    public $db;

    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
    }

    public function addPermission($userID, $permission): bool
    {
        $stmt = $this->db->prepare("INSERT INTO user_permissions (user_id, permission) VALUES (:user_id, :permission)");
        $stmt->bindValue(':user_id', $userID);
        $stmt->bindValue(':permission', $permission);
        return $stmt->execute() !== false;
    }

    public function checkPermission($userID, $permission): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM user_permissions WHERE user_id = :user_id AND permission = :permission");
        $stmt->bindValue(':user_id', $userID);
        $stmt->bindValue(':permission', $permission);
        $result = $stmt->execute();

        return $result && $result->fetchArray() !== false;
    }

    public function changePermission($userID, $permission)
    {
        $stmt = $this->db->prepare("UPDATE user_permissions SET permission = :permission WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID);
        $stmt->bindValue(':permission', $permission);
        return $stmt->execute();
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare("SELECT user_id, permission FROM user_permissions");
        $result = $stmt->execute();
        $users = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }
        return $users;
    }

    /**
     * @throws Exception
     */
    public function addUser($userID, $userPassword, $permission = 'read'): bool
    {
        // Insert a new user with default permission
        $stmt = $this->db->prepare("INSERT INTO users (user_id, password_hash) VALUES (:user_id, :password_hash)");
        $stmt->bindValue(':user_id', $userID);
        $stmt->bindValue(':password_hash', password_hash($userPassword, PASSWORD_DEFAULT));
        if (! $stmt->execute()) {
            throw new Exception("Failed to add user: " . $this->db->lastErrorMsg());
        }

        // Add default permission for the new user
        return $this->addPermission($userID, $permission);
    }

    public function deleteUser($userID): bool
    {
        // Delete user permissions first
        $stmt = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID);
        $stmt->execute();
        // Then delete the user
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userID);
        return $stmt->execute() !== false;
    }
    public function close()
    {
        $this->db->close();
    }
}

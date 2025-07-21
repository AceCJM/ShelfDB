<?php
// src/db/UserPermissions.php
// This file defines the class for authenticating user permissions.

class UserPermissions
{
    private $db;

    public function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
        if (!$this->db) {
            throw new Exception("Could not connect to the database.");
        }
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

    public function close()
    {
        $this->db->close();
    }
}
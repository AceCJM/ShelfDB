<?php
// File src/db/user.php
// This file defines the User class for storing user information.
// User authentication is handled in a separate file.

class User
{
    private $userID;
    private $passwordHash;

    public function __construct($id, $passwordHash)
    {
        $this->userID = $id;
        $this->passwordHash = $passwordHash;
    }

    public function getUserID()
    {
        return $this->userID;
    }
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
}
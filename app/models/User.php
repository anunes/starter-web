<?php

namespace App\Models;

use app\core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role'];

    public function __construct()
    {
        parent::__construct();
    }

    public function createUser($name, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insert_data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ];
        $this->db->insert($this->table, $insert_data);
        return $this->db->lastInsertId();
    }
    public function getUserByEmail($email)
    {
        return $this->db->row("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function getUserById($id)
    {
        $user = $this->db->row("SELECT * FROM users WHERE id = ?", ['id' => $$id]);
        return $user;
    }

    public function updatePasswordResetToken($userId, $token)
    {
        $data = [
            'reset_token' => $token
        ];

        return $this->db->update($this->table, $data, ['id' => $userId]);
    }
}

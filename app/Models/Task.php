<?php

namespace App\Models;

use App\Database;  // Correct namespace reference

class Task
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function create($description)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (description, created_at) VALUES (?, NOW())");
        $stmt->execute([$description]);
    }

    public function toggle($id)
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET completed = NOT completed WHERE id = ?");
        $stmt->execute([$id]);
    }
}

<?php

namespace Models;

use PDO;

class Task
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($name, $board_id, $status, $cover_image)
    {
        // Implementar lógica para crear una nueva tarea
    }

    // Métodos adicionales como readByBoard, update, delete, etc.
}

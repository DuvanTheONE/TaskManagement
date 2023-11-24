<?php

namespace Models;

use PDO;

class Tag
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($name)
    {
        // Implementar lógica para crear una nueva etiqueta
    }

    // Métodos adicionales como update, delete, etc.
}

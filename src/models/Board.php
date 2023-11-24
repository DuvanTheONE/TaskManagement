<?php

namespace Models;

use PDO;

class Board
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllBoards()
    {
        $stmt = $this->pdo->query("SELECT * FROM boards");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBoard($name, $logo = null)
    {
        $logo = $logo ?? 'default_logo.png'; // Asigna un logo por defecto si no se proporciona uno
        $sql = "INSERT INTO boards (name, logo) VALUES (:name, :logo)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $name, ':logo' => $logo]);
        return $this->pdo->lastInsertId();
    }

    public function deleteBoard($id)
    {
        $sql = "DELETE FROM boards WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}

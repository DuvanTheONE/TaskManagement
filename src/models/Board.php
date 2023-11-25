<?php
namespace Models;

use PDO;
use PDOException;

use Models\exceptions\{
    BoardException,
    BoardNotFoundException,
    BoardValidationException,
    BoardCreationException,
    BoardUpdateException,
    BoardDeletionException
};

class Board
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllBoards()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM boards");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new BoardException("Error al obtener los tableros: " . $e->getMessage());
        }
    }

    public function getBoardById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM boards WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $board = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$board) {
                throw new BoardNotFoundException();
            }
            return $board;
        } catch (PDOException $e) {
            throw new BoardException("Error al obtener el tablero: " . $e->getMessage());
        }
    }

    public function createBoard($name, $logo = null)
    {
        try {
            $this->validateBoardData($name, $logo);
            $logo = $logo ?? 'default_logo.png';
            $sql = "INSERT INTO boards (name, logo) VALUES (:name, :logo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':logo' => $logo]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new BoardCreationException("Error al crear el tablero: " . $e->getMessage());
        }
    }

    public function updateBoard($id, $name = null, $logo = null) {
        try {
            if (!is_null($name)) {
                $this->validateBoardData($name);
            }
            $sql = "UPDATE boards SET ";
            $params = [];
            if (!is_null($name)) {
                $sql .= "name = :name, ";
                $params[':name'] = $name;
            }
            if (!is_null($logo)) {
                $sql .= "logo = :logo, ";
                $params[':logo'] = $logo;
            }
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE id = :id";
            $params[':id'] = $id;
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new BoardUpdateException("Error al actualizar el tablero: " . $e->getMessage());
        }
    }
    

    public function deleteBoard($id)
    {
        try {
            $sql = "DELETE FROM boards WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new BoardDeletionException("Error al eliminar el tablero: " . $e->getMessage());
        }
    }

    private function validateBoardData($name) {
        if (empty($name)) {
            throw new BoardValidationException("El nombre del tablero no puede estar vacío.");
        }
    }
}

<?php

namespace Models;

use PDO;
use PDOException;
use Models\exceptions\TagNotFoundException;
use Models\exceptions\TagValidationException;
use Models\exceptions\TagCreationException;
use Models\exceptions\TagUpdateException;
use Models\exceptions\TagDeletionException;

class Tag
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($name)
    {
        if (empty($name)) {
            throw new TagValidationException('El nombre de la etiqueta no puede estar vacío.');
        }

        try {
            $sql = "INSERT INTO tags (name) VALUES (:name)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':name' => $name]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new TagCreationException('Error al crear la etiqueta: ' . $e->getMessage());
        }
    }

    public function readById($id)
    {
        try {
            $sql = "SELECT * FROM tags WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tag) {
                throw new TagNotFoundException('La etiqueta solicitada no existe.');
            }

            return $tag;
        } catch (PDOException $e) {
            throw new TagNotFoundException('Error al leer la etiqueta: ' . $e->getMessage());
        }
    }

    public function readAll()
    {
        try {
            $sql = "SELECT * FROM tags";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new TagNotFoundException('Error al leer las etiquetas: ' . $e->getMessage());
        }
    }

    public function update($id, $name)
    {
        if (empty($name)) {
            throw new TagValidationException('El nombre de la etiqueta no puede estar vacío.');
        }

        try {
            $sql = "UPDATE tags SET name = :name WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new TagUpdateException('No se realizaron cambios en la etiqueta.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TagUpdateException('Error al actualizar la etiqueta: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM tags WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new TagDeletionException('La etiqueta no fue eliminada.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TagDeletionException('Error al eliminar la etiqueta: ' . $e->getMessage());
        }
    }
}

<?php

namespace Models;

use PDO;
use PDOException;
use Models\exceptions\{
    TaskNotFoundException,
    TaskValidationException,
    TaskCreationException,
    TaskUpdateException,
    TaskDeletionException
};

class Task {
    private $pdo;
    private $allowedStatuses = ['Backlog', 'In Progress', 'Done'];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Verifica si el board_id existe en la base de datos
    private function validateBoard($board_id) {
        $stmt = $this->pdo->prepare("SELECT id FROM boards WHERE id = :board_id");
        $stmt->execute([':board_id' => $board_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            throw new TaskValidationException('El board_id proporcionado no existe.');
        }
    }

    // Verifica si el tag_id existe en la base de datos
    private function validateTag($tag_id) {
        $stmt = $this->pdo->prepare("SELECT id FROM tags WHERE id = :tag_id");
        $stmt->execute([':tag_id' => $tag_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            throw new TaskValidationException('El tag_id proporcionado no existe.');
        }
    }

    // Método para crear una tarea
    public function create($name, $board_id, $status = 'Backlog', $cover_image = null) {
        if (empty($name)) {
            throw new TaskValidationException('El nombre de la tarea no puede estar vacío.');
        }

        $this->validateBoard($board_id);

        if (!in_array($status, $this->allowedStatuses)) {
            throw new TaskValidationException('El estado de la tarea no es válido.');
        }

        try {
            $sql = "INSERT INTO tasks (name, board_id, status, cover_image) VALUES (:name, :board_id, :status, :cover_image)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':board_id' => $board_id,
                ':status' => $status,
                ':cover_image' => $cover_image
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new TaskCreationException('Error al crear la tarea: ' . $e->getMessage());
        }
    }

    // Método para obtener todas las tareas de un board
    public function readByBoard($board_id) {
        $this->validateBoard($board_id);

        try {
            $sql = "SELECT * FROM tasks WHERE board_id = :board_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':board_id' => $board_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new TaskNotFoundException('Error al leer las tareas: ' . $e->getMessage());
        }
    }

    // Método para obtener una tarea por su id
    public function readById($id) {
        try {
            $sql = "SELECT * FROM tasks WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$task) {
                throw new TaskNotFoundException('La tarea solicitada no existe.');
            }

            return $task;
        } catch (PDOException $e) {
            throw new TaskNotFoundException('Error al leer la tarea: ' . $e->getMessage());
        }
    }

    // Método para actualizar una tarea
    public function update($id, $name, $status, $cover_image) {
        if (empty($name)) {
            throw new TaskValidationException('El nombre de la tarea no puede estar vacío.');
        }

        if (!in_array($status, $this->allowedStatuses)) {
            throw new TaskValidationException('El estado proporcionado no es válido.');
        }

        $this->readById($id); // Esto lanzará una excepción si la tarea no existe

        try {
            $sql = "UPDATE tasks SET name = :name, status = :status, cover_image = :cover_image WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':status' => $status,
                ':cover_image' => $cover_image
            ]);

            if ($stmt->rowCount() === 0) {
                throw new TaskUpdateException('No se realizaron cambios en la tarea.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskUpdateException('Error al actualizar la tarea: ' . $e->getMessage());
        }
    }

    // Método para eliminar una tarea
    public function delete($id) {
        $this->readById($id); // Esto lanzará una excepción si la tarea no existe

        try {
            $sql = "DELETE FROM tasks WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new TaskDeletionException('La tarea no fue eliminada.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskDeletionException('Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    public function addTagToTask($task_id, $tag_id)
    {
        $existingTask = $this->readById($task_id);
        if (!$existingTask) {
            throw new TaskNotFoundException('La tarea para agregar la etiqueta no existe.');
        }

        if (!$this->tagExists($tag_id)) {
            throw new TaskValidationException('La etiqueta no existe.');
        }

        try {
            $sql = "INSERT INTO task_tags (task_id, tag_id) VALUES (:task_id, :tag_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':task_id' => $task_id,
                ':tag_id' => $tag_id
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new TaskValidationException('Error al agregar la etiqueta a la tarea: ' . $e->getMessage());
        }
    }
    
    public function removeTagFromTask($task_id, $tag_id)
    {
        if (!$this->tagExists($tag_id)) {
            throw new TaskValidationException('La etiqueta no existe.');
        }

        try {
            $sql = "DELETE FROM task_tags WHERE task_id = :task_id AND tag_id = :tag_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':task_id' => $task_id,
                ':tag_id' => $tag_id
            ]);
            
            if($stmt->rowCount() === 0) {
                throw new TaskValidationException('Error al eliminar la etiqueta de la tarea.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskValidationException('Error al eliminar la etiqueta de la tarea: ' . $e->getMessage());
        }
    }

    public function readAll()
    {
        try {
            $sql = "SELECT * FROM tasks";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new TaskNotFoundException('Error al leer las tareas: ' . $e->getMessage());
        }
    }

    private function tagExists($tag_id)
    {
        try {
            $sql = "SELECT * FROM tags WHERE id = :tag_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':tag_id' => $tag_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            throw new TaskValidationException('Error al verificar la existencia de la etiqueta: ' . $e->getMessage());
        }
    }

    public function getTagsByTaskId($task_id)
    {
        $existingTask = $this->readById($task_id);
        if (!$existingTask) {
            throw new TaskNotFoundException('La tarea solicitada para obtener etiquetas no existe.');
        }

        try {
            $sql = "SELECT tags.* FROM tags INNER JOIN task_tags ON tags.id = task_tags.tag_id WHERE task_tags.task_id = :task_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':task_id' => $task_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new TaskNotFoundException('Error al obtener etiquetas de la tarea: ' . $e->getMessage());
        }
    }

    public function setCoverImage($task_id, $cover_image)
    {
        if (empty($cover_image)) {
            throw new TaskValidationException('La imagen de la portada no puede estar vacía.');
        }

        $existingTask = $this->readById($task_id);
        if (!$existingTask) {
            throw new TaskNotFoundException('La tarea a la que intentas asignar una imagen no existe.');
        }

        try {
            $sql = "UPDATE tasks SET cover_image = :cover_image WHERE id = :task_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':task_id' => $task_id,
                ':cover_image' => $cover_image
            ]);

            if($stmt->rowCount() === 0) {
                throw new TaskUpdateException('La imagen de portada no fue actualizada.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskUpdateException('Error al actualizar la imagen de portada: ' . $e->getMessage());
        }
    }

    public function removeCoverImage($task_id)
    {
        $existingTask = $this->readById($task_id);
        if (!$existingTask) {
            throw new TaskNotFoundException('La tarea a la que intentas quitar la imagen no existe.');
        }

        try {
            $sql = "UPDATE tasks SET cover_image = NULL WHERE id = :task_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':task_id' => $task_id]);

            if($stmt->rowCount() === 0) {
                throw new TaskUpdateException('La imagen de portada no fue eliminada.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskUpdateException('Error al quitar la imagen de portada: ' . $e->getMessage());
        }
    }

    public function moveTask($task_id, $status)
    {
        if (empty($status)) {
            throw new TaskValidationException('El estado de la tarea no puede estar vacío.');
        }

        $existingTask = $this->readById($task_id);
        if (!$existingTask) {
            throw new TaskNotFoundException('La tarea a mover no existe.');
        }

        try {
            $sql = "UPDATE tasks SET status = :status WHERE id = :task_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':task_id' => $task_id,
                ':status' => $status
            ]);

            if($stmt->rowCount() === 0) {
                throw new TaskUpdateException('La tarea no se movió a otro estado.');
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new TaskUpdateException('Error al mover la tarea: ' . $e->getMessage());
        }
    }
}
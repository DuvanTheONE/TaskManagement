<?php

namespace Controllers;

use Models\Task;
use Models\exceptions\TaskException;

class TaskController
{
    private $taskModel;

    public function __construct(Task $taskModel)
    {
        $this->taskModel = $taskModel;
    }

    public function createTask($request)
    {
        error_log('Datos recibidos en createTask: ' . print_r($request, true));

        if (empty($request['name'])) {
            error_log('Error: El nombre de la tarea es requerido.');
            return $this->respondJson(['error' => 'El nombre de la tarea es requerido.'], 400);
        }
        if (!isset($request['board_id']) || !is_numeric($request['board_id'])) {
            error_log('Error: ID de tablero no válido o ausente.');
            return $this->respondJson(['error' => 'ID de tablero no válido o ausente.'], 400);
        }

        try {
            $name = $request['name'];
            $board_id = $request['board_id'];
            $status = $request['status'] ?? 'Backlog';
            $cover_image = $request['cover_image'] ?? null;

            $taskId = $this->taskModel->create($name, $board_id, $status, $cover_image);

            return $this->respondJson(['message' => 'Task created successfully', 'task_id' => $taskId], 201);
        } catch (TaskException $e) {
            error_log('Excepción en createTask: ' . $e->getMessage());
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllTasks()
    {
        try {
            $tasks = $this->taskModel->readAll();
            return $this->respondJson(['data' => $tasks], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 404);
        }
    }

    public function getTaskById($id)
    {
        try {
            $task = $this->taskModel->readById($id);
            return $this->respondJson(['data' => $task], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 404);
        }
    }

    public function updateTask($id, $request)
    {
        // Validaciones
        if (empty($request['name'])) {
            return $this->respondJson(['error' => 'El nombre de la tarea es requerido.'], 400);
        }
        if (!is_numeric($id)) {
            return $this->respondJson(['error' => 'ID de tarea no válido.'], 400);
        }

        try {
            $name = $request['name'];
            $status = $request['status'] ?? null;
            $cover_image = $request['cover_image'] ?? null;

            $updatedRows = $this->taskModel->update($id, $name, $status, $cover_image);
            if ($updatedRows > 0) {
                return $this->respondJson(['message' => 'Task updated successfully'], 200);
            } else {
                return $this->respondJson(['message' => 'No changes made to the task'], 200);
            }
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteTask($id)
    {
        try {
            $deletedRows = $this->taskModel->delete($id);
            if ($deletedRows > 0) {
                return $this->respondJson(['message' => 'Task deleted successfully'], 200);
            } else {
                return $this->respondJson(['message' => 'Task not found'], 404);
            }
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function addTagToTask($task_id, $tag_id)
    {
        try {
            $this->taskModel->addTagToTask($task_id, $tag_id);
            return $this->respondJson(['message' => 'Tag added to task successfully'], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function removeTagFromTask($task_id, $tag_id)
    {
        try {
            $this->taskModel->removeTagFromTask($task_id, $tag_id);
            return $this->respondJson(['message' => 'Tag removed from task successfully'], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function setCoverImage($task_id, $cover_image)
    {
        try {
            $this->taskModel->setCoverImage($task_id, $cover_image);
            return $this->respondJson(['message' => 'Cover image set successfully'], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function removeCoverImage($task_id)
    {
        try {
            $this->taskModel->removeCoverImage($task_id);
            return $this->respondJson(['message' => 'Cover image removed successfully'], 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function getTasksByBoard($board_id)
    {
        try {
            $tasks = $this->taskModel->readByBoard($board_id);
            return $this->respondJson($tasks, 200);
        } catch (TaskException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 404);
        }
    }

    private function respondJson($data, $statusCode)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
}

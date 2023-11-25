<?php

require '../../models/exceptions/TaskExceptions.php';

use Models\Task;
use Controllers\TaskController;
use Models\exceptions\TaskException;

require_once '../../../vendor/autoload.php';

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$method = $_SERVER['REQUEST_METHOD'];
$pdo = (require '../../../config/database.php');
$taskModel = new Task($pdo);
$taskController = new TaskController($taskModel);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

function sendJsonResponse($success, $data = null, $error = null) {
    http_response_code($success ? 200 : 400);
    echo json_encode(['success' => $success, 'data' => $data, 'error' => $error]);
    exit;
}

function logError($message) {
    file_put_contents('../../../logs/error_log.log', $message . PHP_EOL, FILE_APPEND);
}

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $task = $taskController->getTaskById($id);
                sendJsonResponse(true, $task);
            } else {
                $tasks = $taskController->getAllTasks();
                sendJsonResponse(true, $tasks);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                sendJsonResponse(false, null, 'El cuerpo de la solicitud no es un JSON válido.');
            }
            if (empty($data['name'])) {
                sendJsonResponse(false, null, 'El nombre de la tarea es requerido.');
            }
            if (!isset($data['board_id']) || !is_numeric($data['board_id'])) {
                sendJsonResponse(false, null, 'ID de tablero no válido o ausente.');
            }
            $taskId = $taskController->createTask($data);
            sendJsonResponse(true, ['taskId' => $taskId]);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null || !$id || empty($data['name'])) {
                sendJsonResponse(false, null, 'ID y nombre de la tarea son obligatorios.');
            }
            $taskController->updateTask($id, $data);
            sendJsonResponse(true, ['message' => 'Tarea actualizada correctamente.']);
            break;
        case 'DELETE':
            if (!$id) {
                sendJsonResponse(false, null, 'ID de tarea es obligatorio para eliminar.');
            }
            $taskController->deleteTask($id);
            sendJsonResponse(true, ['message' => 'Tarea eliminada correctamente.']);
            break;
        case 'OPTIONS':
            http_response_code(204);
            break;
        default:
            sendJsonResponse(false, null, 'Método no permitido');
            break;
    }
} catch (TaskException $e) {
    logError("TaskException: " . $e->getMessage());
    sendJsonResponse(false, null, $e->getMessage());
} catch (ErrorException $e) {
    logError("ErrorException: " . $e->getMessage());
    sendJsonResponse(false, null, "Error de PHP: " . $e->getMessage());
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJsonResponse(false, null, 'Error interno del servidor: ' . $e->getMessage());
} finally {
    restore_error_handler();
}

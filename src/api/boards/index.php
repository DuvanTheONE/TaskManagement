<?php

use Controllers\BoardController;
use Models\exceptions\{
    BoardException,
    BoardValidationException
};

require_once '../../../vendor/autoload.php';
require_once __DIR__ . '../../../models/exceptions/BoardExceptions.php';


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$method = $_SERVER['REQUEST_METHOD'];
$pdo = (require '../../../config/database.php');

$boardController = new BoardController($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        try {
            $boards = $id ? $boardController->show($id) : $boardController->index();
            echo json_encode(['success' => true, 'data' => $boards]);
        } catch (BoardException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            if (!isset($data['name'])) {
                throw new BoardValidationException('El nombre es obligatorio.');
            }
            $boardId = $boardController->store($data['name'], $data['logo'] ?? null);
            echo json_encode(['success' => true, 'boardId' => $boardId]);
        } catch (BoardException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            try {
                if (!$id) {
                    throw new BoardValidationException('ID es obligatorio.');
                }
                // Si el nombre no está establecido, no lo actualices.
                $nameToUpdate = isset($data['name']) ? $data['name'] : null;
                $logoToUpdate = isset($data['logo']) ? $data['logo'] : null;
                $boardController->update($id, $nameToUpdate, $logoToUpdate);
                echo json_encode(['success' => true, 'message' => 'Tablero actualizado correctamente.']);
            } catch (BoardException $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;
    case 'DELETE':
        try {
            if (!$id) {
                throw new BoardValidationException('ID es obligatorio para borrar.');
            }
            $boardController->destroy($id);
            echo json_encode(['success' => true, 'message' => 'Tablero eliminado correctamente.']);
        } catch (BoardException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
    case 'OPTIONS':
        http_response_code(204); // No Content
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        break;
}
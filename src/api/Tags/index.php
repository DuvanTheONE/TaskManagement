<?php

require_once '../../../vendor/autoload.php';
require_once __DIR__ . '../../../models/exceptions/TagExceptions.php';

use Models\Tag;
use Controllers\TagsController;
use Models\exceptions\TagException;
use Models\exceptions\TagValidationException;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$method = $_SERVER['REQUEST_METHOD'];
$pdo = require '../../../config/database.php';

$tagModel = new Tag($pdo);
$tagsController = new TagsController($tagModel);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    switch ($method) {
        case 'GET':
            $tags = $id ? $tagsController->getTagById($id) : $tagsController->getAllTags();
            echo json_encode(['success' => true, 'data' => $tags]);
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $tagId = $tagsController->createTag($data);
            echo json_encode(['success' => true, 'tagId' => $tagId]);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$id) {
                throw new TagValidationException('ID es obligatorio para actualizar.');
            }
            $tagsController->updateTag($id, $data);
            echo json_encode(['success' => true, 'message' => 'Etiqueta actualizada correctamente.']);
            break;
        case 'DELETE':
            if (!$id) {
                throw new TagValidationException('ID es obligatorio para eliminar.');
            }
            $tagsController->deleteTag($id);
            echo json_encode(['success' => true, 'message' => 'Etiqueta eliminada correctamente.']);
            break;
        case 'OPTIONS':
            http_response_code(204); // No Content
            break;
        default:
            throw new Exception('Método no permitido', 405); // Method Not Allowed
    }
} catch (TagException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (TagValidationException $e) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

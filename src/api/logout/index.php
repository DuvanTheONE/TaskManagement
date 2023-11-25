<?php

use Controllers\LogoutController;
use Models\exceptions\AuthenticationException;

require_once '../../../vendor/autoload.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://your-frontend-domain.com"); // Reemplaza con el dominio de tu frontend
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

session_start();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $logoutController = new LogoutController();
        try {
            $logoutController->processLogout();
            echo json_encode(['success' => true, 'message' => 'Logout successful']);
        } catch (AuthenticationException $e) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'OPTIONS':
        http_response_code(204);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        break;
}

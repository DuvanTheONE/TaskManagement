<?php

define('BASE_PATH', realpath(dirname(__FILE__) . '/../..'));

require_once BASE_PATH . '/models/Users.php';
require_once BASE_PATH . '/models/UserExceptions.php';

use Models\User;
use Models\AuthenticationException;

session_start();

// Asegurarse de que solo se procesen las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/views/login.php');
    exit;
}

$pdo = require BASE_PATH . '/config/database.php';

$userModel = new User($pdo);

$username = filter_input(INPUT_POST, 'boardname', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

try {
    $user = $userModel->authenticate($username, $password);

    // Regenerar ID de sesión para la seguridad de la sesión
    session_regenerate_id();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    header('Location: ' . BASE_PATH . '/views/home.php');
    exit;
} catch (AuthenticationException $e) {
    // Mensaje de error genérico para el usuario
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header('Location: ' . BASE_PATH . '/views/login.php');
    exit;
} catch (PDOException $e) {
    // Registra el error pero muestra un mensaje genérico al usuario
    error_log('Error de conexión en process_login: ' . $e->getMessage());
    $_SESSION['error'] = "Error de conexión. Intente más tarde.";
    header('Location: ' . BASE_PATH . '/views/login.php');
    exit;
}


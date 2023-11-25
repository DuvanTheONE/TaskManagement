<?php

define('BASE_PATH', realpath(dirname(__FILE__) . '/../..'));

require_once '../models/Users.php';
require_once '../models/exceptions/UserExceptions.php';

use Models\User;
use Models\exceptions;
use Models\exceptions\AuthenticationException;

session_start();

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

    session_regenerate_id();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    header('Location: http://localhost:5173');
    exit;
} catch (AuthenticationException $e) {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header('Location: ../views/login.php');
    exit;
} catch (PDOException $e) {
    // Registra el error pero muestra un mensaje genérico al usuario
    error_log('Error de conexión en process_login: ' . $e->getMessage());
    $_SESSION['error'] = "Error de conexión. Intente más tarde.";
    header('Location: ' . BASE_PATH . '/views/login.php');
    exit;
}
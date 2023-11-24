<?php

require_once '../Models/Users.php';
use Models\User;
use Models\UserCreationException;

session_start();

function redirectToLogin($message = null) {
    if ($message) {
        $_SESSION['flash'] = $message;
    }
    header('Location: ../Views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $profileImage = filter_input(INPUT_POST, 'profile_image', FILTER_SANITIZE_STRING);

    // Validación
    $errors = [];
    if (!$username) {
        $errors[] = "El nombre de usuario es requerido.";
    }
    if (!$email) {
        $errors[] = "El correo electrónico no es válido.";
    }
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        redirectToLogin();
    } else {
        $pdo = require_once '../../config/database.php';
        $userModel = new User($pdo);

        try {
            $userId = $userModel->create($username, $email, $password, $profileImage);
            redirectToLogin("Registro exitoso. Por favor, inicia sesión.");
        } catch (UserCreationException $e) {
            error_log($e->getMessage());
            redirectToLogin("Hubo un problema al registrar el usuario.");
        } catch (Exception $e) {
            error_log($e->getMessage());
            redirectToLogin("Error inesperado. Por favor, contacta al soporte técnico.");
        }
    }
} else {
    redirectToLogin("Método de solicitud no válido.");
}

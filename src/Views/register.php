<?php 
session_start();

$availableImages = include __DIR__ . '/../../config/profileImages.php';

if (isset($_SESSION['registration_errors'])) {
    foreach ($_SESSION['registration_errors'] as $error) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    unset($_SESSION['registration_errors']);
}

if (isset($_SESSION['success_message'])) {
    echo '<p class="success">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="path_to_your_css/style.css">
    <link rel="stylesheet" href="../../public/css/register.css">
</head>
<body>
    <div class="register-container">
        <form action="../controllers/process_register.php" method="post">
            <h1 class="Register-New-User">New User</h1>
            <div class="profile-image-selector">
                <?php foreach ($availableImages as $imageName): ?>
                    <label for="<?php echo htmlspecialchars($imageName); ?>">
                        <input type="radio" id="<?php echo htmlspecialchars($imageName); ?>" name="profile_image" value="<?php echo htmlspecialchars($imageName); ?>" required>
                        <img src="../../public/profile_images/<?php echo htmlspecialchars($imageName); ?>" alt="Profile Image">
                    </label>
                <?php endforeach; ?>
            </div>
            
            <label for="username">User</label>
            <input type="text" id="username" name="username" required placeholder="e.g.: Jackson Torres">
            
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="e.g.: Default@E-mail.com">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="e.g.: Default">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="buttons-Register">
                <button class="btn-submit-register-user" type="submit">Create User</button>
                <a class="Back-to" href="./login.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

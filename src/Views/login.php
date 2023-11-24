<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #3f3f3f;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #2A2D32;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            width: 450px; 
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-container h2 {
            color: #fff; 
            margin-bottom: 20px;
        }

        .login-container label {
            color: #c7c7c7; 
            margin-bottom: 5px;
            align-self: flex-start;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            background: #333; 
            border: 1px solid #4D515B; 
            border-radius: 15px; 
            padding: 15px;
            color: #fff; 
            margin-bottom: 15px;
            width: calc(100% - 30px); 
        }

        .login-container button {
            background: #3662E3; 
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            margin-bottom: 15px;
        }

        .login-container button:hover {
            background: #2A2D32; 
            color: #878c98;
        }

        .login-container a {
            color: #878c98; 
            text-decoration: none; 
            transition: color 0.3s;
        }

        .login-container a:hover {
            color: #fff; 
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Log-in</h2>
        <form action="../controllers/process_login.php" method="post">
            <label for="boardname">Board Name:</label>
            <input type="text" id="boardname" name="boardname" placeholder="e.g.: Jackson Torres" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="e.g.: Default" required>
            <button type="submit" class="btn-login">Login</button>
            <button type="button" class="btn-cancel" onclick="location.href='index.php'">Cancel</button>
        </form>
    </div>
</body>
</html>

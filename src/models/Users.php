<?php

namespace Models;


use PDO;
use PDOException;
use Models\exceptions\UserNotFoundException;
use Models\exceptions\UserCreationException;
use Models\exceptions\UserUpdateException;
use Models\exceptions\UserDeletionException;
use Models\exceptions\AuthenticationException;

class User
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($username, $email, $password, $profileImage)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password_hash, profile_image) VALUES (:username, :email, :password_hash, :profile_image)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':profile_image', $profileImage);
            $stmt->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Error en User::create - ' . $e->getMessage());
            throw new UserCreationException("Hubo un problema al crear el usuario.");
        }
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new UserNotFoundException("El usuario no fue encontrado.");
            }

            return $user;
        } catch (PDOException $e) {
            error_log('Error en User::findByUsername - ' . $e->getMessage());
            throw new UserNotFoundException("Error al buscar el usuario.");
        }
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new UserNotFoundException("Usuario no encontrado.");
            }

            return $user;
        } catch (PDOException $e) {
            error_log('Error en User::findById - ' . $e->getMessage());
            throw new UserNotFoundException("Error al buscar el usuario por ID.");
        }
    }

    public function update($id, $username, $email, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :username, email = :email, password_hash = :password_hash WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error en User::update - ' . $e->getMessage());
            throw new UserUpdateException("Error al actualizar el usuario.");
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new UserNotFoundException("Usuario no encontrado para eliminar.");
            }
        } catch (PDOException $e) {
            error_log('Error en User::delete - ' . $e->getMessage());
            throw new UserDeletionException("Error al eliminar el usuario.");
        }
    }

    public function authenticate($username, $password)
    {
        try {
            $user = $this->findByUsername($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            } else {
                throw new AuthenticationException("Las credenciales de autenticación no son válidas.");
            }
        } catch (PDOException $e) {
            error_log('Error en User::authenticate - ' . $e->getMessage());
            throw new AuthenticationException("Error en la autenticación del usuario.");
        }
    }
}

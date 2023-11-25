<?php

namespace Models\exceptions;

use Exception;

class BoardException extends Exception {}

class BoardNotFoundException extends BoardException {
    protected $message = 'El tablero solicitado no se encuentra disponible.';
}

class BoardValidationException extends BoardException {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class BoardCreationException extends BoardException {
    protected $message = 'Error al crear el tablero.';
}

class BoardUpdateException extends BoardException {
    protected $message = 'Error al actualizar el tablero.';
}

class BoardDeletionException extends BoardException {
    protected $message = 'Error al eliminar el tablero.';
}
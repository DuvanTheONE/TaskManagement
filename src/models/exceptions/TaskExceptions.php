<?php

namespace Models\exceptions;

use Exception;

class TaskException extends Exception {}

class TaskNotFoundException extends TaskException {
    protected $message = 'La tarea solicitada no se encuentra disponible.';
}

class TaskValidationException extends TaskException {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class TaskCreationException extends TaskException {
    protected $message = 'Error al crear la tarea.';
}

class TaskUpdateException extends TaskException {
    protected $message = 'Error al actualizar la tarea.';
}

class TaskDeletionException extends TaskException {
    protected $message = 'Error al eliminar la tarea.';
}
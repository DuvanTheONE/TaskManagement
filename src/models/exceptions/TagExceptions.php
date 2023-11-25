<?php

namespace Models\exceptions;

use Exception;

class TagException extends Exception {}

class TagNotFoundException extends TagException {
    protected $message = 'La etiqueta solicitada no se encuentra disponible.';
}

class TagValidationException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class TagCreationException extends TagException {
    protected $message = 'Error al crear la etiqueta.';
}

class TagUpdateException extends TagException {
    protected $message = 'Error al actualizar la etiqueta.';
}

class TagDeletionException extends TagException {
    protected $message = 'Error al eliminar la etiqueta.';
}

<?php

namespace Models;

class UserException extends \Exception {}

class UserNotFoundException extends UserException {}
class UserCreationException extends UserException {}
class UserUpdateException extends UserException {}
class UserDeletionException extends UserException {}
class AuthenticationException extends UserException {}

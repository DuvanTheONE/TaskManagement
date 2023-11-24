<?php

namespace Controllers;

use Models\Board;
use PDO;

class BoardController
{
    private $boardModel;

    public function __construct(PDO $pdo)
    {
        $this->boardModel = new Board($pdo);
    }

    public function index()
    {
        return $this->boardModel->getAllBoards();
    }

    public function store($name, $logo = null)
    {
        return $this->boardModel->createBoard($name, $logo);
    }

    public function destroy($id)
    {
        return $this->boardModel->deleteBoard($id);
    }
}
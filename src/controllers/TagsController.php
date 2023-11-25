<?php

namespace Controllers;

use Models\Tag;
use Models\exceptions\TagException;

class TagsController
{
    private $tagModel;

    public function __construct(Tag $tagModel)
    {
        $this->tagModel = $tagModel;
    }

    public function createTag($request)
    {
        try {
            $name = $request['name'] ?? null;
            if (empty($name)) {
                return $this->respondJson(['error' => 'El nombre de la etiqueta es requerido.'], 400);
            }

            $tagId = $this->tagModel->create($name);
            return $this->respondJson(['message' => 'Etiqueta creada con éxito', 'tag_id' => $tagId], 201);
        } catch (TagException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllTags()
    {
        try {
            $tags = $this->tagModel->readAll();
            return $this->respondJson(['data' => $tags], 200);
        } catch (TagException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 404);
        }
    }

    public function getTagById($id)
    {
        try {
            $tag = $this->tagModel->readById($id);
            if (!$tag) {
                return $this->respondJson(['error' => 'Etiqueta no encontrada.'], 404);
            }
            return $this->respondJson(['data' => $tag], 200);
        } catch (TagException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function updateTag($id, $request)
    {
        try {
            $name = $request['name'] ?? null;
            if (empty($name)) {
                return $this->respondJson(['error' => 'El nombre de la etiqueta es requerido.'], 400);
            }

            $this->tagModel->update($id, $name);
            return $this->respondJson(['message' => 'Etiqueta actualizada con éxito'], 200);
        } catch (TagException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteTag($id)
    {
        try {
            $this->tagModel->delete($id);
            return $this->respondJson(['message' => 'Etiqueta eliminada con éxito'], 200);
        } catch (TagException $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    private function respondJson($data, $statusCode)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}

const BASE_URL = 'http://localhost/Task_Manager_proyecto_6to_semestre/src/api/boards/';

export const fetchBoards = async (boardId = null) => {
    const url = boardId ? `${BASE_URL}?id=${boardId}` : BASE_URL;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Error al recuperar los tableros');
        }
        const result = await response.json();
        return result.data;
    } catch (error) {
        console.error('Hubo un problema al recuperar los tableros:', error);
    }
};

export const addBoard = async (boardName, boardLogo = null) => {
    try {
        const response = await fetch(BASE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: boardName, logo: boardLogo }),
        });
        if (!response.ok) {
            throw new Error('Error al crear el tablero');
        }
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Hubo un problema al crear el tablero:', error);
    }
};

export const updateBoard = async (boardId, newName, newLogo) => {
    const body = JSON.stringify({
        ...(newName !== undefined && { name: newName }),
        ...(newLogo !== undefined && { logo: newLogo }),
    });

    try {
        const response = await fetch(`${BASE_URL}?id=${boardId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: body,
        });
        if (!response.ok) {
            throw new Error('Error al actualizar el tablero');
        }
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Hubo un problema al actualizar el tablero:', error);
    }
};


export const deleteBoard = async (boardId) => {
    try {
        const response = await fetch(`${BASE_URL}?id=${boardId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Error al eliminar el tablero');
        }
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Hubo un problema al eliminar el tablero:', error);
    }
};

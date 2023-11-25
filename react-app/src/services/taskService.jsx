const TASKS_BASE_URL = 'http://localhost/Task_Manager_proyecto_6to_semestre/src/api/tasks/';

export const fetchTasks = async (boardId) => {
    const url = `${TASKS_BASE_URL}?board_id=${boardId}`;
    try {
        const response = await fetch(url);
        const text = await response.text();
        console.log(text);
        try {
            const result = JSON.parse(text);
            if (response.ok) {
                return result.data || []; 
            } else {
                throw new Error(result.error || 'Error desconocido al recuperar las tareas');
            }
        } catch (error) {
            console.error('Error al analizar la respuesta:', text);
            throw new Error(`Error al analizar la respuesta: ${error.message}`);
        }
    } catch (error) {
        console.error('Hubo un problema al recuperar las tareas:', error);
        throw error;
    }
};

export const addTask = async (taskData) => {
    try {
        const response = await fetch(TASKS_BASE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(taskData),
        });

        const responseBody = await response.text();
        console.log('Response:', responseBody); 

        if (!response.ok) {
            const errorData = JSON.parse(responseBody);
            console.error('Error al crear la tarea:', errorData.error);
            throw new Error(errorData.error);
        }

        return JSON.parse(responseBody);
    } catch (error) {
        console.error('Hubo un problema al crear la tarea:', error);
        throw error;
    }
};

export const updateTask = async (taskId, taskData) => {
    try {
        const response = await fetch(`${TASKS_BASE_URL}?id=${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(taskData),
        });
        if (!response.ok) {
            const errorData = await response.text();
            const error = errorData ? JSON.parse(errorData).error : response.statusText;
            throw new Error(`Error al actualizar la tarea: ${error}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Hubo un problema al actualizar la tarea:', error);
        throw error;
    }
};

export const deleteTask = async (taskId) => {
    try {
        const response = await fetch(`${TASKS_BASE_URL}?id=${taskId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            const errorData = await response.text();
            const error = errorData ? JSON.parse(errorData).error : response.statusText;
            throw new Error(`Error al eliminar la tarea: ${error}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Hubo un problema al eliminar la tarea:', error);
        throw error;
    }
};

export default {
    fetchTasks,
    addTask,
    updateTask,
    deleteTask,
};
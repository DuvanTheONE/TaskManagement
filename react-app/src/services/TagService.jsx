const TAGS_BASE_URL = 'http://localhost/Task_Manager_proyecto_6to_semestre/src/api/tags/';

const fetchHeaders = {
    'Content-Type': 'application/json',
    // Añade aquí cualquier otro encabezado que tu API necesite, como tokens de autorización.
};

const TagService = {
    getAllTags: async () => {
        try {
            const response = await fetch(TAGS_BASE_URL, { headers: fetchHeaders });
            const data = await response.json();
            if (response.ok) {
                return data.data; // Asumiendo que la API devuelve los tags en una propiedad 'data'.
            } else {
                throw new Error(data.error || 'Error desconocido al recuperar las etiquetas');
            }
        } catch (error) {
            console.error("Error fetching tags:", error);
            throw error;
        }
    },

    getTagById: async (id) => {
        try {
            const response = await fetch(`${TAGS_BASE_URL}?id=${id}`, { headers: fetchHeaders });
            const data = await response.json();
            if (response.ok) {
                return data.data;
            } else {
                throw new Error(data.error || 'Error desconocido al recuperar la etiqueta');
            }
        } catch (error) {
            console.error("Error fetching tag by id:", error);
            throw error;
        }
    },

    createTag: async (tagName) => {
        try {
            const response = await fetch(TAGS_BASE_URL, {
                method: 'POST',
                headers: fetchHeaders,
                body: JSON.stringify({ name: tagName }),
            });
            const data = await response.json();
            if (response.ok) {
                return data.tagId; // Asumiendo que la API devuelve el id de la etiqueta creada.
            } else {
                throw new Error(data.error || 'Error desconocido al crear la etiqueta');
            }
        } catch (error) {
            console.error("Error creating tag:", error);
            throw error;
        }
    },

    updateTag: async (id, updatedTagData) => {
        try {
            const response = await fetch(`${TAGS_BASE_URL}?id=${id}`, {
                method: 'PUT',
                headers: fetchHeaders,
                body: JSON.stringify(updatedTagData),
            });
            const data = await response.json();
            if (response.ok) {
                return data.message; // Asumiendo que la API devuelve un mensaje de éxito.
            } else {
                throw new Error(data.error || 'Error desconocido al actualizar la etiqueta');
            }
        } catch (error) {
            console.error("Error updating tag:", error);
            throw error;
        }
    },

    deleteTag: async (id) => {
        try {
            const response = await fetch(`${TAGS_BASE_URL}?id=${id}`, {
                method: 'DELETE',
                headers: fetchHeaders,
            });
            const data = await response.json();
            if (response.ok) {
                return data.message; // Asumiendo que la API devuelve un mensaje de éxito.
            } else {
                throw new Error(data.error || 'Error desconocido al eliminar la etiqueta');
            }
        } catch (error) {
            console.error("Error deleting tag:", error);
            throw error;
        }
    },
};

export default TagService;

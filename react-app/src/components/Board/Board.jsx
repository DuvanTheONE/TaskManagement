import React, { useState, useEffect } from 'react';
import taskService from '../../services/taskService';
import TaskForm from '../TaskForm/TaskForm';
import './Board.scss';


const Board = ({ boardId }) => {
    const [tasks, setTasks] = useState([]);
    const [showTaskForm, setShowTaskForm] = useState(false);
    const [taskStatusToAdd, setTaskStatusToAdd] = useState('');

    useEffect(() => {
        const loadTasks = async () => {
            try {
                const tasksFromService = await taskService.fetchTasks(boardId);
                if (Array.isArray(tasksFromService)) {
                    setTasks(tasksFromService);
                } else {
                    console.error('La respuesta no es un array:', tasksFromService);
                    setTasks([]); // Asegura que tasks es siempre un array.
                }
            } catch (error) {
                console.error('Error al cargar las tareas:', error);
                setTasks([]); // Asegura que tasks es siempre un array.
            }
        };

        loadTasks();
    }, [boardId]);

    const categorizedTasks = {
        backlog: tasks.filter(task => task.status === 'Backlog'),
        inProgress: tasks.filter(task => task.status === 'In Progress'),
        inReview: tasks.filter(task => task.status === 'In Review'),
        completed: tasks.filter(task => task.status === 'Completed'),
    };

    const handleShowTaskForm = (status) => {
        setTaskStatusToAdd(status);
        setShowTaskForm(true);
    };

    const handleTaskSubmit = async (taskData) => {
        try {
            await taskService.addTask({ ...taskData, status: taskStatusToAdd, boardId });
            setShowTaskForm(false);
            const updatedTasks = await taskService.fetchTasks(boardId);
            setTasks(updatedTasks);
        } catch (error) {
            console.error('Error al crear la tarea:', error);
        }
    };

    return (
        <div className="board">
            {Object.entries(categorizedTasks).map(([status, tasks]) => (
                <div key={status} className="board-column">
                    <h3>{status} ({tasks.length})</h3>
                    <button onClick={() => handleShowTaskForm(status)}>Add Task</button>
                </div>
            ))}
            {showTaskForm && (
                <TaskForm
                    boardId={boardId}
                    status={taskStatusToAdd}
                    onSubmit={handleTaskSubmit}
                    onCancel={() => setShowTaskForm(false)}
                />
            )}
        </div>
    );
};

export default Board;

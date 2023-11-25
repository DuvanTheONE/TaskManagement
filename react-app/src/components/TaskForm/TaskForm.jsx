import React, { useState } from 'react';
import TagService from '../../services/TagService';
import taskService from '../../services/taskService';
import './TaskForm.scss';

const TaskForm = ({ status, boardId, onCancel }) => {
    const validStatuses = ["Backlog", "InProgress", "InReview", "Completed"];
    const initialStatus = validStatuses.includes(status) ? status : 'Backlog';
    const [selectedStatus, setSelectedStatus] = useState(initialStatus);
    const [taskName, setTaskName] = useState('');
    const [description, setDescription] = useState('');
    const [tags, setTags] = useState([]);
    const [tagInput, setTagInput] = useState('');

    const handleAddTag = () => {
        const newTags = tagInput.split(',')
            .map(tag => tag.trim())
            .filter(tag => tag && !tags.includes(tag));
        if (newTags.length > 0) {
            setTags([...tags, ...newTags]);
            setTagInput('');
        }
    };

    const handleTagInputChange = (e) => {
        setTagInput(e.target.value);
    };

    const handleTagInputKeyDown = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleAddTag();
        }
    };

    const handleRemoveTag = (tagToRemove) => {
        setTags(tags.filter(tag => tag !== tagToRemove));
    };

    const saveTagToDatabase = async (tagName) => {
        try {
            const response = await TagService.createTag(tagName);
            return response.tagId;
        } catch (error) {
            console.error('Error creating tag:', error);
            throw error; 
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!boardId || isNaN(boardId)) {
            console.error('El boardId es inválido:', boardId);
            return;
        }

        try {
            const tagPromises = tags.map(tag => saveTagToDatabase(tag));
            const tagIds = await Promise.all(tagPromises);

            const taskData = {
                name: taskName,
                description,
                status: selectedStatus,
                board_id: boardId,
                tags: tagIds,
            };

            const result = await taskService.addTask(taskData);
            console.log('Task created successfully', result);

            setTaskName('');
            setDescription('');
            setSelectedStatus('');
            setTags([]);
            onCancel(); // Esta función debe ser proporcionada por el componente padre
        } catch (error) {
            console.error('Error submitting form:', error);
        }
    };

    return (
        <div className="task-form-container">
            <div className="task-form">
                <div className="form-header">
                    <h2>New Task</h2>
                    <button className="close-button" onClick={onCancel}>×</button>
                </div>
                <form onSubmit={handleSubmit} className="form-body">
                    <label>
                        Task Name
                        <input
                            type="text"
                            value={taskName}
                            onChange={(e) => setTaskName(e.target.value)}
                            required
                        />
                    </label>
                    <label>
                        Description
                        <textarea
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                        />
                    </label>
                    <label>
                        Status
                        <select value={selectedStatus} onChange={(e) => setSelectedStatus(e.target.value)}>
                            <option value="Backlog">Backlog</option>
                            <option value="InProgress">In Progress</option>
                            <option value="InReview">In Review</option>
                            <option value="Completed">Completed</option>
                        </select>

                    </label>
                    <label>
                        Tags
                        <div className="tags-container">
                            {tags.map((tag, index) => (
                                <span key={index} className="selected-tag">
                                    {tag}
                                    <button type="button" onClick={() => handleRemoveTag(tag)}>x</button>
                                </span>
                            ))}
                            <input
                                type="text"
                                value={tagInput}
                                onChange={handleTagInputChange}
                                onKeyDown={handleTagInputKeyDown}
                                placeholder="Type tags separated by commas"
                            />
                        </div>
                    </label>
                    <div className="form-footer">
                        <button type="submit" className="create-task">Create Task</button>
                        <button type="button" className="cancel" onClick={onCancel}>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default TaskForm;

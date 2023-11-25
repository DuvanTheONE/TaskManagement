// TaskCard.jsx
const TaskCard = ({ task }) => {
    return (
        <div className="task-card">
            {task.cover_image && <img src={task.cover_image} alt="Cover" className="task-image" />}
            <div className="task-details">
                <h4>{task.name}</h4>
                <div className="task-tags">
                    {task.tags.map((tag, index) => (
                        <span key={index} className="tag">{tag}</span>
                    ))}
                </div>
            </div>
        </div>
    );
};

import React, { useState, useEffect, useRef } from 'react';
import moreIcon from '/images/board_icons/mas.png';
import editIcon from '/images/board_icons/editar.png';
import changeIcon from '/images/board_icons/intercambiar.png';
import deleteIcon from '/images/board_icons/eliminar.png';
import { logos } from '../../../public/images/board_icons/Logos';
import './BoardList.scss';

const BoardList = ({ boards, onRename, onChangeIcon, onDelete, onBoardSelect }) => {
    const [selectedBoardId, setSelectedBoardId] = useState(null);
    const [isOptionsVisible, setIsOptionsVisible] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editedName, setEditedName] = useState('');
    const [isIconSelectorVisible, setIsIconSelectorVisible] = useState(false);
    const boardListRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (boardListRef.current && !boardListRef.current.contains(event.target)) {
                setIsOptionsVisible(false);
                setIsIconSelectorVisible(false);
                setIsEditing(false);
                setSelectedBoardId(null);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const handleSelectBoard = (boardId) => {
        setSelectedBoardId(boardId);
        setIsOptionsVisible(selectedBoardId !== boardId || !isOptionsVisible);
        onBoardSelect(boardId); // Notificamos al componente padre
    };


    const handleRenameClick = (boardId, boardName) => {
        setIsEditing(true);
        setEditedName(boardName);
        setSelectedBoardId(boardId);
        setIsOptionsVisible(false);
    };

    const handleRenameSubmit = (e) => {
        e.preventDefault();
        onRename(selectedBoardId, editedName);
        setIsEditing(false);
        setSelectedBoardId(null);
    };

    const handleIconChangeClick = (boardId) => {
        setIsIconSelectorVisible(!isIconSelectorVisible);
        setIsOptionsVisible(false);
        setSelectedBoardId(boardId);
    };

    return (
        <div className="board-list" ref={boardListRef}>
            {boards.map((board) => (
                <div 
                    key={board.id} 
                    className={`board ${selectedBoardId === board.id ? 'selected' : ''}`}
                    onClick={() => handleSelectBoard(board.id)}
                >
                    <img src={board.logo} alt={board.name} className="board-icon" />
                    {isEditing && selectedBoardId === board.id ? (
                        <form onSubmit={handleRenameSubmit}>
                            <input 
                                type="text" 
                                value={editedName}
                                onChange={(e) => setEditedName(e.target.value)}
                                autoFocus
                            />
                        </form>
                    ) : (
                        <span className="board-name">{board.name}</span>
                    )}
                    <img 
                        src={moreIcon} 
                        alt="more options" 
                        className="options-button"
                        onClick={(e) => {
                            e.stopPropagation();
                            handleSelectBoard(board.id);
                        }}
                    />
                    {isOptionsVisible && selectedBoardId === board.id && (
                        <div className="board-actions">
                            <button onClick={() => handleRenameClick(board.id, board.name)}>
                                <img src={editIcon} alt="Rename" />
                                Rename
                            </button>
                            <button onClick={() => handleIconChangeClick(board.id)}>
                                <img src={changeIcon} alt="Change Icon" />
                                Change Icono
                            </button>
                            <button onClick={() => onDelete(board.id)}>
                                <img src={deleteIcon} alt="Delete Board" />
                                Delete
                            </button>
                        </div>
                    )}
                    {isIconSelectorVisible && selectedBoardId === board.id && (
                        <div className="icon-selector">
                            {logos.map(logo => (
                                <img 
                                    key={logo.id} 
                                    src={logo.src} 
                                    alt={logo.alt} 
                                    className= "icon-image"
                                    onClick={() => {
                                        onChangeIcon(board.id, logo.src);
                                        setIsIconSelectorVisible(false);
                                    }}
                                />
                            ))}
                        </div>
                    )}
                </div>
            ))}
        </div>
    );
};

export default BoardList;

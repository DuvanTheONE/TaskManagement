import React, { useState } from 'react';
import { addBoard } from '../../services/boardService';
import { logos } from '../../../public/images/board_icons/Logos';
import './BoardCard.scss';

const BoardCard = ({ onBoardCreated, onClose }) => {
    const [boardName, setBoardName] = useState('');
    const [selectedLogo, setSelectedLogo] = useState(logos[0].src);

    const handleCreateBoard = async () => {
        try {
            const result = await addBoard(boardName, selectedLogo);
            if (result) {
                onBoardCreated(result);
                onClose();
            } else {
                throw new Error('Error al crear el tablero');
            }
        } catch (error) {
            console.error('Error al crear el tablero:', error);
        }
    };

    return (
        <div className="board-card">
            <input
                type="text"
                placeholder="e.g.: Default Board"
                value={boardName}
                onChange={(e) => setBoardName(e.target.value)}
            />
            <div className="logos-container">
                {logos.map((logo) => (
                    <img
                        key={logo.id}
                        src={logo.src}
                        alt={logo.alt}
                        className={selectedLogo === logo.src ? 'selected' : ''}
                        onClick={() => setSelectedLogo(logo.src)}
                    />
                ))}
            </div>
            <button onClick={handleCreateBoard}>Create Board</button>
        </div>
    );
};

export default BoardCard;

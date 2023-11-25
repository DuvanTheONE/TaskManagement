import React, { useState, useEffect } from 'react';
import Sidebar from '../components/Sidebar/Sidebar';
import BoardCard from '../components/BoarCard/BoardCard';
import Board from '../components/Board/Board';
import { fetchBoards, deleteBoard, updateBoard } from '../services/boardService';
import './Dashboard.scss'

function Dashboard() {
    const [showBoardCard, setShowBoardCard] = useState(false);
    const [boards, setBoards] = useState([]);
    const [activeBoardId, setActiveBoardId] = useState(null);

    useEffect(() => {
        const loadBoards = async () => {
            const boardsData = await fetchBoards();
            if (boardsData) {
                setBoards(boardsData);
            }
        };

        loadBoards();
    }, []);

    const handleBoardCreated = (newBoard) => {
        setBoards(prevBoards => [...prevBoards, newBoard]);
        setShowBoardCard(false);
    };

    const handleRenameBoard = async (boardId, newName) => {
        const currentBoard = boards.find(board => board.id === boardId);
        if (!currentBoard) return;
    
        const updatedBoard = await updateBoard(boardId, newName, currentBoard.logo);
        if (updatedBoard) {
            setBoards(prevBoards =>
                prevBoards.map(board =>
                    board.id === boardId ? { ...board, name: newName } : board
                )
            );
        }
    };
    

    const handleChangeBoardIcon = async (boardId, newIcon) => {
        const updatedBoard = await updateBoard(boardId, null, newIcon);
        if (updatedBoard) {
            setBoards(prevBoards =>
                prevBoards.map(board =>
                    board.id === boardId ? { ...board, logo: newIcon } : board
                )
            );
        }
    };

    const handleDeleteBoard = async (boardId) => {
        const deletedBoard = await deleteBoard(boardId);
        if (deletedBoard) {
            setBoards(prevBoards => prevBoards.filter(board => board.id !== boardId));
        }
    };

    return (
        <div className="dashboard">
            <Sidebar
                boards={boards}
                onAddBoardClick={() => setShowBoardCard(true)}
                onRename={handleRenameBoard}
                onChangeIcon={handleChangeBoardIcon}
                onDelete={handleDeleteBoard}
                onBoardSelect={setActiveBoardId}
            />
            {activeBoardId && <Board boardId={activeBoardId} />}
            {showBoardCard && (
                <BoardCard
                    onBoardCreated={handleBoardCreated}
                    onClose={() => setShowBoardCard(false)}
                />
            )}
        </div>
    );
}

export default Dashboard;

import React from 'react';
import { Link } from 'react-router-dom';
import BoardList from '../BoardList/BoardList';
import { useTheme } from '../../context/ThemeContext';
import './Sidebar.scss'

const Sidebar = ({ boards, onAddBoardClick, onRename, onChangeIcon, onDelete, onBoardSelect }) => {
    const { theme, toggleTheme } = useTheme();

    return (
        <aside className={`sidebar ${theme}`}>
            <div className="logo">
                <h2>MyTask</h2>
            </div>
            <nav>
                <BoardList
                    boards={boards}
                    onRename={onRename}
                    onChangeIcon={onChangeIcon}
                    onDelete={onDelete}
                    onBoardSelect={onBoardSelect}
                />
                <ul className="nav-list">
                    <li className="nav-item">
                        <button onClick={onAddBoardClick}>
                            Add new Board
                        </button>
                    </li>
                </ul>
                <div className="settings">
                    <Link to="/settings">Settings</Link>
                </div>
                <div className="logout">
                    <Link to="">Log out</Link>
                </div>
                <div className="theme-toggle">
                    <button onClick={toggleTheme}>
                        {theme === 'light' ? 'Dark' : 'Light'} Mode
                    </button>
                </div>
            </nav>
        </aside>
    );
};

export default Sidebar;

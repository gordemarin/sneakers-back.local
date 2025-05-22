import React from 'react';
import './Header.css';

const Header = () => {
  return (
    <header className="header">
      <div className="header-left">
        <div className="logo">
          <img src="/image 4-1.png" alt="VUE КЕДЫ" />
          <div className="logo-text">
            <h3>VUE КЕДЫ</h3>
            <p>Магазин лучших кроссовок</p>
          </div>
        </div>
      </div>
      <div className="header-right">
        <button className="header-button favorite">
          <img src="/Vector-1.svg" alt="Избранное" />
          <span>Избранное</span>
        </button>
        <button className="header-button profile">
          <img src="/Vector.svg" alt="Профиль" />
          <span>Профиль</span>
        </button>
      </div>
    </header>
  );
};

export default Header; 
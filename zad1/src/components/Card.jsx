import React, { useState } from 'react';
import './Card.css';

const Card = ({ image, title, price, isSelected: initialIsSelected }) => {
  const [isFavorite, setIsFavorite] = useState(false);
  const [isSelected, setIsSelected] = useState(initialIsSelected);

  const toggleFavorite = (e) => {
    e.preventDefault();
    setIsFavorite(!isFavorite);
  };

  const toggleSelected = (e) => {
    e.preventDefault();
    setIsSelected(!isSelected);
    console.log('Товар ' + (isSelected ? 'удален из корзины' : 'добавлен в корзину'));
  };

  return (
    <div className="card">
      <button 
        className={`favorite-btn ${isFavorite ? 'active' : ''}`}
        onClick={toggleFavorite}
        aria-label={isFavorite ? "Удалить из избранного" : "Добавить в избранное"}
      >
        <img 
          src={isFavorite ? "/zmdi_favorite-outline-1.svg" : "/zmdi_favorite-outline.svg"} 
          alt="В избранное" 
        />
      </button>
      <div className="card-image">
        <img src={image} alt={title} />
      </div>
      <h5 className="card-title">{title}</h5>
      <div className="card-footer">
        <div className="card-price">
          <span className="price-label">ЦЕНА:</span>
          <div className="price">{price} руб.</div>
        </div>
        <button 
          className={`add-btn ${isSelected ? 'selected' : ''}`}
          onClick={toggleSelected}
          aria-label={isSelected ? "Удалить из корзины" : "Добавить в корзину"}
        >
          <img 
            src={isSelected ? "/Group 91.svg" : "/Group 95.svg"} 
            alt={isSelected ? "Добавлено" : "Добавить"} 
          />
        </button>
      </div>
    </div>
  );
};

export default Card; 
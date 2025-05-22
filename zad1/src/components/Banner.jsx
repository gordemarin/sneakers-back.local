import React from 'react';
import './Banner.css';

const Banner = () => {
  const handleBannerClick = () => {
    // клик на баннер
    console.log("Баннер clicked");
    // window.location.href = и тд;
  };

  const handlePrevClick = (e) => {
    e.stopPropagation(); 
    console.log("Previous clicked");
    // Логика переключения на предыдущий баннер
  };

  const handleNextClick = (e) => {
    e.stopPropagation(); 
    console.log("Next clicked");
    // Логика переключения на следующий баннер
  };

  return (
    <div className="banner-container" onClick={handleBannerClick}>
      <img 
        src="/Group 112.jpg" 
        alt="Stan Smith Forever Banner" 
        className="banner-full-image" 
      />
      <div className="banner-navigation">
        <button className="banner-nav-btn prev" onClick={handlePrevClick}>
          <img src="/Vector 217-1.svg" alt="Previous" />
        </button>
        <button className="banner-nav-btn next" onClick={handleNextClick}>
          <img src="/Vector 217.svg" alt="Next" />
        </button>
      </div>
    </div>
  );
};

export default Banner; 
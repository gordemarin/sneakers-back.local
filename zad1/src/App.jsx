import React from 'react';
import Header from './components/Header';
import Banner from './components/Banner';
import CardList from './components/CardList';
import './App.css';

function App() {
  return (
    <div className="container">
      <Header />
      {/* Что может быть хуже чем откусить яблоко и увидеть внутри червя?  */}
      
      {/* Холокост */}
      <div className="content">
        <Banner />
        <CardList />
      </div>
    </div>
  );
}

export default App;

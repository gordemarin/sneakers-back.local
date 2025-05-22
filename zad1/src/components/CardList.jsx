import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Card from './Card';
import './CardList.css';

// Тестовые данные на случай, если API недоступен
const fallbackData = [
  {
    id: 1,
    name: 'Мужские Кроссовки Nike Blazer Mid Suede',
    price: 12999,
    image: '/image 5-1.jpg'
  },
  {
    id: 2,
    name: 'Мужские Кроссовки Nike Air Max 270',
    price: 15600,
    image: '/image 5-2.jpg'
  },
  {
    id: 3,
    name: 'Мужские Кроссовки Nike Blazer Mid Suede',
    price: 8499,
    image: '/image 5-3.jpg'
  },
  {
    id: 4,
    name: 'Кроссовки Puma X Aka Boku Future Rider',
    price: 8999,
    image: '/image 4.png'
  }
];

const CardList = () => {
  const [sneakers, setSneakers] = useState(fallbackData);
  const [loading, setLoading] = useState(true);
  const [usingFallback, setUsingFallback] = useState(false);

  useEffect(() => {
    // Таймер безопасности - если API не ответит за 800мс, используем локальные данные
    const fallbackTimer = setTimeout(() => {
      if (loading) {
        console.log('API не ответил вовремя, используем локальные данные');
        setSneakers(fallbackData);
        setLoading(false);
        setUsingFallback(true);
      }
    }, 800);

    // Загрузка данных с API
    const fetchData = async () => {
      try {
        console.log('Запрашиваем данные с API');
        
        // Пробуем сначала тестовое API Laravel
        let response;
        try {
          response = await axios.get('http://sneakers-back.local/api/test-sneakers', {
            headers: { 
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: 2000
          });
          console.log('Успешно получили данные с тестового API Laravel');
        } catch (testApiError) {
          console.log('Тестовое API Laravel недоступно, пробуем PHP файл:', testApiError.message);
          
          try {
            // Если тестовое API недоступно, пробуем PHP файл
            response = await axios.get('http://sneakers-back.local/test-api.php', {
              headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              timeout: 2000
            });
            console.log('Успешно получили данные с PHP файла');
          } catch (phpFileError) {
            console.log('PHP файл недоступен, пробуем основное API:', phpFileError.message);
            
            try {
              // Если PHP файл недоступен, пробуем основное API
              response = await axios.get('http://sneakers-back.local/api/sneakers', {
                headers: { 
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                },
                timeout: 3000
              });
              console.log('Успешно получили данные с основного API');
            } catch (mainApiError) {
              console.log('Основное API недоступно, используем локальный JSON файл:', mainApiError.message);
              
              // Если все API недоступны, получаем данные из локального файла
              response = await axios.get('/api-data.json', {
                timeout: 1000
              });
              console.log('Получили данные из локального JSON файла');
            }
          }
        }
        
        // Проверяем структуру данных
        if (response.data && typeof response.data === 'object') {
          let apiData = null;
          
          // Поддерживаем разные форматы ответа
          if (Array.isArray(response.data)) {
            apiData = response.data;
          } else if (Array.isArray(response.data.data)) {
            apiData = response.data.data;
          }
          
          if (apiData && apiData.length > 0) {
            console.log('Получены данные с API:', apiData);
            setSneakers(apiData);
            setUsingFallback(false);
          } else {
            throw new Error('API вернул пустой массив данных');
          }
        } else {
          throw new Error('Некорректный формат данных API');
        }
      } catch (error) {
        console.error('Ошибка при загрузке данных:', error);
        setSneakers(fallbackData);
        setUsingFallback(true);
      } finally {
        clearTimeout(fallbackTimer);
        setLoading(false);
      }
    };

    fetchData();
    
    return () => clearTimeout(fallbackTimer);
  }, []);

  return (
    <div className="card-list-container">
      <div className="card-list-header">
        <h1 className="section-title">Все кроссовки</h1>
        {usingFallback && (
          <div className="api-status">
            Используются тестовые данные
          </div>
        )}
      </div>
      
      {loading ? (
        <div className="loading">Загрузка...</div>
      ) : (
        <div className="card-list">
          {sneakers.map(sneaker => (
            <Card 
              key={sneaker.id}
              image={sneaker.image_url || sneaker.image || '/placeholder.jpg'}
              title={sneaker.name || sneaker.title}
              price={sneaker.price}
              isSelected={sneaker.is_favorite || sneaker.isSelected || false}
            />
          ))}
        </div>
      )}
    </div>
  );
};

export default CardList; 
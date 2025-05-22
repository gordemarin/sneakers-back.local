# Интеграция API с фронтендом

## Настройка CORS для фронтенда

### Обновление конфигурации CORS на бэкенде

В файле `config/cors.php` настройте разрешенные домены:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',    // React dev server
        'http://localhost:8080',    // Vue dev server  
        'http://localhost:5173',    // Vite dev server
        'https://your-frontend-domain.com', // Продакшн домен
        // Добавьте ваши домены
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## Примеры использования API во фронтенде

### React (с axios)

```javascript
// api.js
import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Получение списка кроссовок
export const getSneakers = async (params = {}) => {
  try {
    const response = await api.get('/sneakers', { params });
    return response.data;
  } catch (error) {
    console.error('Ошибка при получении кроссовок:', error);
    throw error;
  }
};

// Получение детальной информации о кроссовке
export const getSneaker = async (id) => {
  try {
    const response = await api.get(`/sneakers/${id}`);
    return response.data;
  } catch (error) {
    console.error('Ошибка при получении кроссовки:', error);
    throw error;
  }
};

// Получение брендов
export const getBrands = async () => {
  try {
    const response = await api.get('/brands');
    return response.data;
  } catch (error) {
    console.error('Ошибка при получении брендов:', error);
    throw error;
  }
};

// Получение категорий
export const getCategories = async () => {
  try {
    const response = await api.get('/categories');
    return response.data;
  } catch (error) {
    console.error('Ошибка при получении категорий:', error);
    throw error;
  }
};

// Переключение избранного
export const toggleFavorite = async (id) => {
  try {
    const response = await api.post(`/sneakers/${id}/toggle-favorite`);
    return response.data;
  } catch (error) {
    console.error('Ошибка при переключении избранного:', error);
    throw error;
  }
};
```

### Vue.js (с Pinia и axios)

```javascript
// stores/sneakers.js
import { defineStore } from 'pinia';
import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

export const useSneakersStore = defineStore('sneakers', {
  state: () => ({
    sneakers: [],
    sneaker: null,
    brands: [],
    categories: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchSneakers(params = {}) {
      this.loading = true;
      try {
        const response = await api.get('/sneakers', { params });
        this.sneakers = response.data.data;
      } catch (error) {
        this.error = error.message;
        console.error('Ошибка при загрузке кроссовок:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchSneaker(id) {
      this.loading = true;
      try {
        const response = await api.get(`/sneakers/${id}`);
        this.sneaker = response.data.data;
      } catch (error) {
        this.error = error.message;
        console.error('Ошибка при загрузке кроссовки:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchBrands() {
      try {
        const response = await api.get('/brands');
        this.brands = response.data.data;
      } catch (error) {
        this.error = error.message;
        console.error('Ошибка при загрузке брендов:', error);
      }
    },

    async fetchCategories() {
      try {
        const response = await api.get('/categories');
        this.categories = response.data.data;
      } catch (error) {
        this.error = error.message;
        console.error('Ошибка при загрузке категорий:', error);
      }
    },

    async toggleFavorite(id) {
      try {
        const response = await api.post(`/sneakers/${id}/toggle-favorite`);
        // Обновляем состояние локально
        const sneaker = this.sneakers.find(s => s.id === id);
        if (sneaker) {
          sneaker.is_favorite = response.data.data.is_favorite;
        }
        return response.data;
      } catch (error) {
        this.error = error.message;
        console.error('Ошибка при переключении избранного:', error);
        throw error;
      }
    },
  },
});
```

### Next.js (с SWR)

```javascript
// lib/api.js
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('API request failed:', error);
      throw error;
    }
  }

  // GET запросы
  async get(endpoint, params = {}) {
    const searchParams = new URLSearchParams(params).toString();
    const url = searchParams ? `${endpoint}?${searchParams}` : endpoint;
    return this.request(url);
  }

  // POST запросы
  async post(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }
}

export const apiService = new ApiService();

// Хуки для использования с SWR
export const fetcher = (url) => apiService.get(url);

// hooks/useSneakers.js
import useSWR from 'swr';
import { fetcher } from '../lib/api';

export function useSneakers(params = {}) {
  const searchParams = new URLSearchParams(params).toString();
  const key = searchParams ? `/sneakers?${searchParams}` : '/sneakers';
  
  const { data, error, mutate } = useSWR(key, fetcher);

  return {
    sneakers: data?.data || [],
    loading: !error && !data,
    error,
    mutate,
  };
}

export function useSneaker(id) {
  const { data, error, mutate } = useSWR(
    id ? `/sneakers/${id}` : null,
    fetcher
  );

  return {
    sneaker: data?.data || null,
    loading: !error && !data,
    error,
    mutate,
  };
}
```

## Обработка ошибок

### Универсальный обработчик ошибок

```javascript
// utils/errorHandler.js
export const handleApiError = (error) => {
  if (error.response) {
    // Сервер ответил с кодом ошибки
    const { status, data } = error.response;
    
    switch (status) {
      case 404:
        return 'Ресурс не найден';
      case 422:
        // Ошибки валидации
        return data.errors ? 
          Object.values(data.errors).flat().join(', ') : 
          'Ошибка валидации данных';
      case 500:
        return 'Внутренняя ошибка сервера';
      default:
        return data.message || 'Произошла ошибка';
    }
  } else if (error.request) {
    // Запрос был отправлен, но ответа нет
    return 'Сервер не отвечает';
  } else {
    // Ошибка при настройке запроса
    return 'Ошибка при отправке запроса';
  }
};
```

## Переменные окружения

### .env файлы для разных фронтенд фреймворков

**React (.env):**
```env
REACT_APP_API_URL=https://your-api-domain.com/api
```

**Vue.js (.env):**
```env
VITE_API_URL=https://your-api-domain.com/api
```

**Next.js (.env.local):**
```env
NEXT_PUBLIC_API_URL=https://your-api-domain.com/api
```

## Примеры компонентов

### React компонент для списка кроссовок

```jsx
// components/SneakersList.jsx
import React, { useState, useEffect } from 'react';
import { getSneakers } from '../api';

const SneakersList = () => {
  const [sneakers, setSneakers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const loadSneakers = async () => {
      try {
        setLoading(true);
        const data = await getSneakers();
        setSneakers(data.data);
      } catch (err) {
        setError('Ошибка при загрузке кроссовок');
      } finally {
        setLoading(false);
      }
    };

    loadSneakers();
  }, []);

  if (loading) return <div>Загрузка...</div>;
  if (error) return <div>Ошибка: {error}</div>;

  return (
    <div className="sneakers-grid">
      {sneakers.map((sneaker) => (
        <div key={sneaker.id} className="sneaker-card">
          <img 
            src={sneaker.images[0]?.url || '/placeholder.jpg'} 
            alt={sneaker.name}
          />
          <h3>{sneaker.name}</h3>
          <p>{sneaker.price_formatted}</p>
          <p>{sneaker.brand?.name}</p>
        </div>
      ))}
    </div>
  );
};

export default SneakersList;
```

## Тестирование интеграции

### Проверка подключения к API

```javascript
// utils/apiTest.js
const testApiConnection = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/test-sneakers`, {
      headers: {
        'Accept': 'application/json',
      },
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    
    const data = await response.json();
    console.log('✅ API подключение успешно:', data);
    return true;
  } catch (error) {
    console.error('❌ Ошибка подключения к API:', error);
    return false;
  }
};

// Вызов при инициализации приложения
testApiConnection();
```

## Безопасность

### Защита от CSRF (если используете)

```javascript
// Добавление CSRF токена
const getCsrfToken = async () => {
  await api.get('/sanctum/csrf-cookie');
};

// Использование перед важными запросами
await getCsrfToken();
await api.post('/sneakers/1/toggle-favorite');
```

## Кеширование

### Использование React Query

```javascript
// hooks/useSneakersQuery.js
import { useQuery, useMutation, useQueryClient } from 'react-query';
import { getSneakers, toggleFavorite } from '../api';

export const useSneakersQuery = (params) => {
  return useQuery(
    ['sneakers', params],
    () => getSneakers(params),
    {
      staleTime: 5 * 60 * 1000, // 5 минут
      cacheTime: 10 * 60 * 1000, // 10 минут
    }
  );
};

export const useToggleFavoriteMutation = () => {
  const queryClient = useQueryClient();

  return useMutation(toggleFavorite, {
    onSuccess: () => {
      // Обновляем кеш после успешного изменения
      queryClient.invalidateQueries(['sneakers']);
    },
  });
};
```

После настройки всех компонентов ваш фронтенд будет готов к работе с API! 
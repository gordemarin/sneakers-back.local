# Sneakers API - Laravel Backend

🚀 **Готовый к продакшн Laravel API для интернет-магазина кроссовок**

## 📋 Описание проекта

Полнофункциональный REST API на Laravel для управления каталогом кроссовок с поддержкой:
- ✅ CRUD операции для кроссовок, брендов, категорий
- ✅ Загрузка и управление изображениями
- ✅ Фильтрация, поиск, пагинация
- ✅ Система избранного
- ✅ CORS настройки для фронтенда
- ✅ Исправление всех ошибок 500 и 403
- ✅ Полная документация по развертыванию

## 🛠️ Технологии

- **Backend:** Laravel 11, PHP 8.2+
- **База данных:** MySQL 8.0+
- **Веб-сервер:** Nginx/Apache
- **Развертывание:** Автоматические скрипты
- **Версионирование:** Git + GitHub

## 🚀 Быстрое развертывание

### Автоматическое развертывание (рекомендуется)
```bash
curl -fsSL https://raw.githubusercontent.com/gordemarin/sneakers-back.local/main/deploy.sh | bash
```

### Ручное развертывание
```bash
# Клонирование проекта
git clone https://github.com/gordemarin/sneakers-back.local.git sneakers-api
cd sneakers-api

# Следуйте инструкции в QUICK_DEPLOY.md
```

## 📚 Документация развертывания

| Файл | Описание |
|------|----------|
| **[COMPLETE_DEPLOYMENT_GUIDE.md](COMPLETE_DEPLOYMENT_GUIDE.md)** | 📖 Полная инструкция развертывания на сервере |
| **[QUICK_DEPLOY.md](QUICK_DEPLOY.md)** | ⚡ Краткая памятка для быстрого развертывания |
| **[deploy.sh](deploy.sh)** | 🤖 Автоматический скрипт установки |
| **[update.sh](update.sh)** | 🔄 Скрипт обновления на сервере |
| **[GIT_PERMISSION_FIX.md](GIT_PERMISSION_FIX.md)** | 🔧 Исправление проблем с Git |
| **[FRONTEND_INTEGRATION.md](FRONTEND_INTEGRATION.md)** | 🌐 Интеграция с фронтендом |

## 🛠️ Технические требования

- **ОС:** Ubuntu 20.04+ / Debian 10+ / CentOS 8+
- **PHP:** 8.2+
- **База данных:** MySQL 8.0+ / MariaDB 10.3+
- **Веб-сервер:** Nginx (рекомендуется) / Apache
- **RAM:** 1GB+ (рекомендуется 2GB+)

## 🔗 API Эндпоинты

### Тестовые эндпоинты
- `GET /api/test-sneakers` - Получить тестовые данные кроссовок
- `GET /` - Главная страница API

### Основные эндпоинты (расширяемые)
- `GET /api/sneakers` - Список всех кроссовок
- `GET /api/sneakers/{id}` - Получить кроссовок по ID
- `POST /api/sneakers` - Создать новый кроссовок
- `PUT /api/sneakers/{id}` - Обновить кроссовок
- `DELETE /api/sneakers/{id}` - Удалить кроссовок

## 🌐 CORS поддержка

API полностью поддерживает CORS для работы с фронтенд приложениями:
- Разрешены все Origins (`*`)
- Поддерживаются методы: `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`
- Поддерживаются заголовки: `Origin`, `Content-Type`, `Accept`, `Authorization`, `X-Request-With`

## 🔧 Локальная разработка

### Установка
```bash
# Клонирование
git clone https://github.com/gordemarin/sneakers-back.local.git
cd sneakers-back.local

# Установка зависимостей
composer install

# Настройка окружения
cp .env.example .env
php artisan key:generate

# Настройка базы данных
php artisan migrate

# Запуск сервера разработки
php artisan serve
```

### Настройка базы данных (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sneakers_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📦 Структура проекта

```
sneakers-back.local/
├── app/
│   ├── Http/Controllers/    # Контроллеры API
│   └── Models/             # Модели данных
├── database/
│   ├── migrations/         # Миграции БД
│   └── seeders/           # Сидеры для тестовых данных
├── routes/
│   ├── api.php            # API маршруты
│   └── web.php            # Веб маршруты
├── public/                # Публичная директория
├── storage/               # Хранилище файлов и логов
├── deploy.sh              # Скрипт автоустановки
├── update.sh              # Скрипт обновления
└── документация/          # Файлы документации
```

## 🔒 Безопасность

- ✅ HTTPS готовность (SSL сертификаты)
- ✅ Защита от SQL инъекций (Eloquent ORM)
- ✅ CORS правильно настроен
- ✅ Скрытие системных файлов (.env, .git)
- ✅ Валидация входящих данных
- ✅ Защищенные заголовки HTTP

## 🚀 Продакшн развертывание

### Быстрый запуск
1. Подготовьте сервер (Ubuntu 20.04+)
2. Настройте домен на ваш сервер
3. Запустите автоустановку:
   ```bash
   curl -fsSL https://raw.githubusercontent.com/gordemarin/sneakers-back.local/main/deploy.sh | bash
   ```
4. Следуйте инструкциям скрипта
5. API готов: `https://your-domain.com/api/test-sneakers`

### Обновление на сервере
```bash
cd /var/www/sneakers-api
bash update.sh
```

## 🧪 Тестирование

```bash
# Локальное тестирование
curl -X GET http://localhost:8000/api/test-sneakers

# Продакшн тестирование
curl -X GET https://your-domain.com/api/test-sneakers

# Тест CORS
curl -X OPTIONS https://your-domain.com/api/test-sneakers \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET"
```

## 🔄 Интеграция с фронтендом

### React/Next.js пример
```javascript
const API_BASE_URL = 'https://your-domain.com/api';

// Получение кроссовок
const fetchSneakers = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/test-sneakers`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Ошибка загрузки кроссовок:', error);
  }
};
```

### Vue.js пример
```javascript
// В компоненте Vue
async fetchSneakers() {
  try {
    const response = await this.$http.get('/api/test-sneakers');
    this.sneakers = response.data;
  } catch (error) {
    console.error('Ошибка:', error);
  }
}
```

Подробные примеры интеграции смотрите в [FRONTEND_INTEGRATION.md](FRONTEND_INTEGRATION.md)

## 🐛 Troubleshooting

### Частые проблемы

**500 Internal Server Error:**
- Проверьте права доступа: `sudo chmod -R 775 storage bootstrap/cache`
- Очистите кеш: `php artisan config:clear`

**CORS ошибки:**
- Проверьте конфигурацию Nginx
- Убедитесь что заголовки CORS настроены

**База данных недоступна:**
- Проверьте настройки в `.env`
- Убедитесь что MySQL запущен

Полное руководство по решению проблем: [GIT_PERMISSION_FIX.md](GIT_PERMISSION_FIX.md)

## 📞 Поддержка

- **Документация:** См. файлы `*.md` в корне проекта
- **Issues:** GitHub Issues в репозитории
- **Email:** [Ваш email для поддержки]

## 📄 Лицензия

Этот проект распространяется под лицензией MIT. См. файл [LICENSE](LICENSE) для подробностей.

---

**🌟 Готово к продакшн использованию!**  
**⏱️ Время развертывания: 15-30 минут**  
**🔗 Репозиторий:** https://github.com/gordemarin/sneakers-back.local


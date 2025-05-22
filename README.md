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

## 🚀 Быстрый старт

### Способ 1: Через GitHub (рекомендуется)

1. **Создайте репозиторий на GitHub** и загрузите код (см. `GITHUB_SETUP.md`)
2. **На сервере выполните:**
```bash
ssh username@your-server-ip
sudo git clone https://github.com/YOUR_USERNAME/sneakers-api.git /var/www/sneakers-api
cd /var/www/sneakers-api
sudo bash deploy.sh
```

### Способ 2: Через архив

1. **Создайте пакет:** `bash create-package.sh`
2. **Загрузите на сервер:** `scp sneakers-api-*.tar.gz username@server:/home/username/`
3. **Распакуйте и разверните:**
```bash
sudo mkdir -p /var/www/sneakers-api
sudo tar -xzf sneakers-api-*.tar.gz -C /var/www/sneakers-api
cd /var/www/sneakers-api
sudo bash deploy.sh
```

## 📚 Документация

| Файл | Описание |
|------|----------|
| [`GITHUB_SETUP.md`](GITHUB_SETUP.md) | Пошаговая загрузка на GitHub |
| [`DEPLOYMENT_INSTRUCTIONS.md`](DEPLOYMENT_INSTRUCTIONS.md) | Полная инструкция по развертыванию |
| [`QUICK_START.md`](QUICK_START.md) | Быстрый старт и основные команды |
| [`UPLOAD_TO_SERVER_INSTRUCTIONS.md`](UPLOAD_TO_SERVER_INSTRUCTIONS.md) | Все способы загрузки на сервер |
| [`FRONTEND_INTEGRATION.md`](FRONTEND_INTEGRATION.md) | Интеграция с фронтендом |
| [`IMAGES_FIX_INSTRUCTIONS.md`](IMAGES_FIX_INSTRUCTIONS.md) | Решение проблем с изображениями |
| [`API_FIX_INSTRUCTIONS.md`](API_FIX_INSTRUCTIONS.md) | Исправление ошибок API |

## 🔗 API Эндпоинты

### Основные маршруты:
```
GET    /api/sneakers              - Список кроссовок (с фильтрами)
GET    /api/sneakers/{id}         - Детали кроссовки  
GET    /api/brands                - Список брендов
GET    /api/categories            - Список категорий
GET    /api/sneakers/brand/{slug} - Кроссовки по бренду
GET    /api/sneakers/category/{slug} - Кроссовки по категории
POST   /api/sneakers/{id}/toggle-favorite - Переключить избранное
GET    /api/test-sneakers         - Тестовые данные
```

### Управление изображениями:
```
GET    /api/sneakers/{id}/images  - Список изображений
POST   /api/sneakers/{id}/images  - Загрузка изображения
DELETE /api/sneakers/{id}/images  - Удаление изображения
```

## 🔧 Требования к серверу

- **OS:** Ubuntu 20.04+ / CentOS 8+ / Debian 10+
- **RAM:** 2GB+ (рекомендуется 4GB)
- **Disk:** 20GB+
- **PHP:** 8.2+
- **Database:** MySQL 8.0+ / PostgreSQL 13+
- **Web Server:** Nginx / Apache

## 🛡️ Безопасность

- ✅ CORS правильно настроен
- ✅ Валидация всех входящих данных
- ✅ Защита от SQL-инъекций (Eloquent ORM)
- ✅ Обработка ошибок без утечки информации
- ✅ Фильтрация загружаемых файлов

## 🔄 Обновление

```bash
# На локальной машине
git add .
git commit -m "Описание изменений"
git push origin main

# На сервере
cd /var/www/sneakers-api
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo php artisan migrate --force
sudo php artisan config:cache
sudo systemctl restart php8.2-fpm
```

## 🐛 Устранение проблем

### API возвращает ошибку 500:
```bash
sudo tail -f /var/log/nginx/sneakers-api.error.log
sudo tail -f /var/www/sneakers-api/storage/logs/laravel.log
```

### Проблемы с изображениями:
```bash
sudo php artisan storage:link
sudo chmod -R 755 /var/www/sneakers-api/storage/app/public
```

### CORS ошибки:
Обновите `config/cors.php` с доменом вашего фронтенда.

## 💻 Локальная разработка

```bash
# Клонирование проекта
git clone https://github.com/YOUR_USERNAME/sneakers-api.git
cd sneakers-api

# Установка зависимостей
composer install

# Настройка окружения
cp .env.example .env
php artisan key:generate

# Настройка базы данных
php artisan migrate
php artisan db:seed

# Запуск сервера разработки
php artisan serve
```

## 🌟 Особенности

- **Автоматическое развертывание** одной командой
- **Исправлены все известные ошибки** (500, 403, CORS)
- **Правильная структура изображений** и их обработка
- **Полная документация** для разработчиков
- **Готовые примеры** интеграции с фронтендом
- **Оптимизация для продакшн** окружения

## 📞 Поддержка

- 📖 Полная документация в папке проекта
- 🐛 Все известные ошибки исправлены
- 🔧 Готовые скрипты развертывания
- 💡 Примеры интеграции с популярными фронтенд фреймворками

---

**🎯 Готов к продакшн использованию!** Следуйте инструкциям в документации для быстрого развертывания.


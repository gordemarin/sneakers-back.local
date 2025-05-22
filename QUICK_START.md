# Быстрый старт развертывания Sneakers API

## Способ 1: Автоматическое развертывание (рекомендуется)

### Подготовка:
1. Подключитесь к вашему серверу по SSH
2. Загрузите файлы проекта на сервер

### Запуск автоматического развертывания:
```bash
# Скачайте скрипт развертывания
wget https://raw.githubusercontent.com/your-repo/deploy.sh

# Или скопируйте файл deploy.sh на сервер и выполните:
sudo bash deploy.sh
```

Скрипт автоматически:
- Установит все необходимые компоненты (PHP, MySQL, Nginx, Composer)
- Настроит базу данных
- Сконфигурирует веб-сервер
- Развернет ваше приложение
- Настроит SSL (опционально)

## Способ 2: Ручное развертывание

Если вы предпочитаете контролировать каждый шаг, следуйте подробной инструкции в файле `DEPLOYMENT_INSTRUCTIONS.md`.

## После развертывания

### Проверка работоспособности:
```bash
# Проверить статус служб
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# Тестирование API
curl -H "Accept: application/json" http://your-domain.com/api/test-sneakers
```

### Настройка фронтенда:
В вашем фронтенд приложении укажите URL API:
```env
# React
REACT_APP_API_URL=https://your-domain.com/api

# Vue.js
VITE_API_URL=https://your-domain.com/api

# Next.js
NEXT_PUBLIC_API_URL=https://your-domain.com/api
```

## Основные эндпоинты API:

- `GET /api/sneakers` - список кроссовок
- `GET /api/sneakers/{id}` - детали кроссовки
- `GET /api/brands` - список брендов
- `GET /api/categories` - список категорий
- `GET /api/sneakers/brand/{brand}` - кроссовки по бренду
- `GET /api/sneakers/category/{category}` - кроссовки по категории
- `POST /api/sneakers/{id}/toggle-favorite` - добавить/убрать из избранного
- `GET /api/test-sneakers` - тестовые данные

## Устранение проблем:

### API возвращает ошибку 500:
```bash
# Проверьте логи
sudo tail -f /var/log/nginx/sneakers-api.error.log
sudo tail -f /var/www/sneakers-api/storage/logs/laravel.log

# Очистите кеши
cd /var/www/sneakers-api
sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan route:clear
```

### Проблемы с изображениями:
```bash
# Пересоздайте символическую ссылку
cd /var/www/sneakers-api
sudo php artisan storage:link

# Проверьте права доступа
sudo chmod -R 755 storage/app/public
```

### CORS ошибки:
Убедитесь, что в `config/cors.php` правильно настроены разрешенные домены для вашего фронтенда.

## Обновление приложения:

```bash
cd /var/www/sneakers-api

# Получить обновления из Git
sudo git pull origin main

# Обновить зависимости
sudo composer install --no-dev --optimize-autoloader

# Очистить кеши
sudo php artisan cache:clear
sudo php artisan config:cache
sudo php artisan route:cache

# Выполнить миграции (если есть новые)
sudo php artisan migrate --force

# Перезапустить службы
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## Поддержка

Если возникают проблемы:
1. Проверьте логи ошибок
2. Убедитесь, что все службы запущены
3. Проверьте настройки файрвола
4. Убедитесь, что домен правильно настроен

📞 **Контакты для поддержки:** [ваши контактные данные] 
# 🚀 Полная инструкция развертывания Sneakers API

## 📋 Содержание
1. [Требования к серверу](#требования-к-серверу)
2. [Подготовка сервера](#подготовка-сервера)
3. [Установка LEMP стека](#установка-lemp-стека)
4. [Клонирование проекта](#клонирование-проекта)
5. [Настройка Laravel](#настройка-laravel)
6. [Конфигурация Nginx](#конфигурация-nginx)
7. [Настройка SSL](#настройка-ssl)
8. [Финальная проверка](#финальная-проверка)
9. [Автоматизация обновлений](#автоматизация-обновлений)
10. [Troubleshooting](#troubleshooting)

---

## 🖥️ Требования к серверу

### Минимальные требования:
- **ОС:** Ubuntu 20.04+ / Debian 10+ / CentOS 8+
- **RAM:** 1 GB (рекомендуется 2 GB)
- **Диск:** 10 GB свободного места
- **CPU:** 1 vCPU
- **Сеть:** Доступ к интернету

### Рекомендуемые требования:
- **ОС:** Ubuntu 22.04 LTS
- **RAM:** 2-4 GB
- **Диск:** 20+ GB SSD
- **CPU:** 2+ vCPU

---

## 🔧 Подготовка сервера

### Шаг 1: Обновление системы

```bash
# Подключение к серверу
ssh root@YOUR_SERVER_IP

# Обновление пакетов
sudo apt update && sudo apt upgrade -y

# Установка базовых утилит
sudo apt install -y curl wget git unzip software-properties-common
```

### Шаг 2: Создание пользователя (если работаете под root)

```bash
# Создание пользователя
adduser deploy
usermod -aG sudo deploy

# Переключение на пользователя
su - deploy
```

### Шаг 3: Настройка файрволла

```bash
# Установка UFW
sudo apt install -y ufw

# Разрешение SSH, HTTP, HTTPS
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https

# Включение файрволла
sudo ufw enable
```

---

## 🐘 Установка LEMP стека

### Шаг 1: Установка Nginx

```bash
sudo apt install -y nginx

# Запуск и автозагрузка
sudo systemctl start nginx
sudo systemctl enable nginx

# Проверка статуса
sudo systemctl status nginx
```

### Шаг 2: Установка MySQL

```bash
# Установка MySQL
sudo apt install -y mysql-server

# Безопасная настройка
sudo mysql_secure_installation

# Создание базы данных и пользователя
sudo mysql -u root -p
```

В MySQL выполните:
```sql
CREATE DATABASE sneakers_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sneakers_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON sneakers_api.* TO 'sneakers_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Шаг 3: Установка PHP 8.2

```bash
# Добавление репозитория PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Установка PHP и расширений
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-curl \
php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-soap php8.2-intl php8.2-readline \
php8.2-dom php8.2-filter php8.2-hash php8.2-openssl php8.2-pcre php8.2-pdo \
php8.2-session php8.2-tokenizer php8.2-ctype php8.2-fileinfo php8.2-ftp \
php8.2-gd php8.2-iconv

# Запуск PHP-FPM
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

### Шаг 4: Установка Composer

```bash
# Скачивание и установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Проверка установки
composer --version
```

---

## 📦 Клонирование проекта

### Шаг 1: Клонирование из GitHub

```bash
# Переход в директорию веб-сервера
cd /var/www

# Клонирование проекта
sudo git clone https://github.com/gordemarin/sneakers-back.local.git sneakers-api

# Изменение владельца
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
```

### Шаг 2: Настройка прав доступа

```bash
cd /var/www/sneakers-api

# Установка правильных прав
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

# Специальные права для Laravel
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

---

## ⚙️ Настройка Laravel

### Шаг 1: Установка зависимостей

```bash
cd /var/www/sneakers-api

# Установка PHP зависимостей
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### Шаг 2: Конфигурация окружения

```bash
# Копирование файла окружения
sudo cp .env.example .env

# Редактирование настроек
sudo nano .env
```

В файле `.env` настройте:
```env
APP_NAME="Sneakers API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sneakers_api
DB_USERNAME=sneakers_user
DB_PASSWORD=your_strong_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Шаг 3: Генерация ключей и миграции

```bash
# Генерация ключа приложения
sudo -u www-data php artisan key:generate

# Выполнение миграций
sudo -u www-data php artisan migrate

# Заполнение базы данных (если есть сидеры)
sudo -u www-data php artisan db:seed

# Кеширование конфигурации
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Создание символической ссылки для хранилища
sudo -u www-data php artisan storage:link
```

---

## 🌐 Конфигурация Nginx

### Шаг 1: Создание конфигурации сайта

```bash
sudo nano /etc/nginx/sites-available/sneakers-api
```

Содержимое файла:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    
    # Редирект на HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    root /var/www/sneakers-api/public;
    index index.php index.html index.htm;

    # SSL сертификаты (будут настроены позже)
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # SSL настройки
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Gzip сжатие
    gzip on;
    gzip_types text/plain application/json application/javascript text/css application/xml;
    
    # CORS заголовки
    add_header 'Access-Control-Allow-Origin' '*' always;
    add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
    add_header 'Access-Control-Allow-Headers' 'Origin, Content-Type, Accept, Authorization, X-Request-With' always;
    add_header 'Access-Control-Allow-Credentials' 'true' always;

    # Обработка preflight запросов
    if ($request_method = 'OPTIONS') {
        add_header 'Access-Control-Allow-Origin' '*' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
        add_header 'Access-Control-Allow-Headers' 'Origin, Content-Type, Accept, Authorization, X-Request-With' always;
        add_header 'Access-Control-Max-Age' 1728000;
        add_header 'Content-Type' 'text/plain; charset=utf-8';
        add_header 'Content-Length' 0;
        return 204;
    }

    # Основная конфигурация Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Обработка PHP файлов
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Безопасность: скрытие .htaccess и других системных файлов
    location ~ /\. {
        deny all;
    }

    # Кеширование статических файлов
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Логи
    access_log /var/log/nginx/sneakers-api.access.log;
    error_log /var/log/nginx/sneakers-api.error.log;
}
```

### Шаг 2: Активация сайта

```bash
# Создание символической ссылки
sudo ln -s /etc/nginx/sites-available/sneakers-api /etc/nginx/sites-enabled/

# Удаление дефолтной конфигурации
sudo rm /etc/nginx/sites-enabled/default

# Проверка конфигурации
sudo nginx -t

# Перезапуск Nginx (пока без SSL)
sudo systemctl reload nginx
```

---

## 🔒 Настройка SSL

### Временная конфигурация без SSL

Сначала создайте временную конфигурацию без SSL:

```bash
sudo nano /etc/nginx/sites-available/sneakers-api-temp
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    
    root /var/www/sneakers-api/public;
    index index.php index.html index.htm;

    # CORS заголовки
    add_header 'Access-Control-Allow-Origin' '*' always;
    add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
    add_header 'Access-Control-Allow-Headers' 'Origin, Content-Type, Accept, Authorization, X-Request-With' always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

```bash
# Замена конфигурации
sudo rm /etc/nginx/sites-enabled/sneakers-api
sudo ln -s /etc/nginx/sites-available/sneakers-api-temp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Установка SSL сертификата

```bash
# Установка Certbot
sudo apt install -y certbot python3-certbot-nginx

# Получение SSL сертификата
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Восстановление основной конфигурации
sudo rm /etc/nginx/sites-enabled/sneakers-api-temp
sudo ln -s /etc/nginx/sites-available/sneakers-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Автообновление SSL

```bash
# Тест автообновления
sudo certbot renew --dry-run

# Настройка cron для автообновления
sudo crontab -e
```

Добавьте строку:
```
0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ✅ Финальная проверка

### Шаг 1: Проверка служб

```bash
# Проверка статуса всех служб
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# Проверка портов
sudo netstat -tlnp | grep :80
sudo netstat -tlnp | grep :443
sudo netstat -tlnp | grep :3306
```

### Шаг 2: Тестирование API

```bash
# Тест основного эндпоинта
curl -X GET https://your-domain.com/api/test-sneakers

# Тест с CORS
curl -X OPTIONS https://your-domain.com/api/test-sneakers \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET" \
  -H "Access-Control-Request-Headers: Content-Type"

# Проверка статуса Laravel
curl -X GET https://your-domain.com/
```

### Шаг 3: Проверка логов

```bash
# Логи Nginx
sudo tail -f /var/log/nginx/sneakers-api.error.log
sudo tail -f /var/log/nginx/sneakers-api.access.log

# Логи Laravel
sudo tail -f /var/www/sneakers-api/storage/logs/laravel.log

# Логи PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
```

---

## 🔄 Автоматизация обновлений

### Скрипт автоматического обновления

Создайте файл `/var/www/sneakers-api/update.sh`:

```bash
sudo nano /var/www/sneakers-api/update.sh
```

```bash
#!/bin/bash

# Скрипт обновления Sneakers API
set -e

PROJECT_DIR="/var/www/sneakers-api"
cd $PROJECT_DIR

echo "🔄 Начинаем обновление Sneakers API..."

# Проверка прав доступа
if [ ! -w ".git" ]; then
    echo "⚠️  Исправляем права доступа..."
    sudo chown -R www-data:www-data .
fi

# Удаление lock файлов
if [ -f ".git/index.lock" ]; then
    echo "🔓 Удаляем заблокированные файлы Git..."
    rm -f .git/index.lock
fi

# Сохранение изменений
if ! git diff --quiet 2>/dev/null || ! git diff --cached --quiet 2>/dev/null; then
    echo "💾 Сохраняем локальные изменения..."
    git add .
    git commit -m "Auto-save before update: $(date)"
fi

# Получение обновлений
echo "📥 Получаем обновления..."
git pull origin main

# Обновление зависимостей
echo "📦 Обновляем зависимости..."
sudo -u www-data composer install --no-dev --optimize-autoloader

# Миграции
echo "🗄️ Выполняем миграции..."
sudo -u www-data php artisan migrate --force

# Очистка кешей
echo "🧹 Очищаем кеши..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Права доступа
echo "🔒 Устанавливаем права доступа..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# Перезапуск служб
echo "🔄 Перезапускаем службы..."
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

echo "✅ Обновление завершено!"
echo "🌐 Проверьте: https://your-domain.com/api/test-sneakers"
```

```bash
# Сделать скрипт исполняемым
sudo chmod +x /var/www/sneakers-api/update.sh
```

---

## 🔧 Troubleshooting

### Проблема: 500 Internal Server Error

```bash
# Проверьте логи
sudo tail -f /var/log/nginx/sneakers-api.error.log
sudo tail -f /var/www/sneakers-api/storage/logs/laravel.log

# Проверьте права доступа
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage
sudo chmod -R 775 /var/www/sneakers-api/bootstrap/cache

# Очистите кеши
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
```

### Проблема: База данных недоступна

```bash
# Проверьте подключение к MySQL
mysql -u sneakers_user -p sneakers_api

# Проверьте настройки в .env
sudo nano /var/www/sneakers-api/.env

# Перезапустите MySQL
sudo systemctl restart mysql
```

### Проблема: CORS ошибки

```bash
# Проверьте конфигурацию Nginx
sudo nginx -t

# Перезагрузите Nginx
sudo systemctl reload nginx

# Проверьте заголовки
curl -I https://your-domain.com/api/test-sneakers
```

### Проблема: SSL сертификат

```bash
# Проверьте сертификат
sudo certbot certificates

# Обновите сертификат
sudo certbot renew

# Проверьте конфигурацию SSL
openssl s_client -connect your-domain.com:443
```

---

## 📊 Мониторинг

### Настройка логирования

```bash
# Создание директории для логов
sudo mkdir -p /var/log/sneakers-api

# Настройка ротации логов
sudo nano /etc/logrotate.d/sneakers-api
```

Содержимое файла ротации:
```
/var/www/sneakers-api/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    copytruncate
    su www-data www-data
}
```

### Мониторинг производительности

```bash
# Мониторинг процессов
htop

# Мониторинг дискового пространства
df -h

# Мониторинг памяти
free -h

# Проверка соединений
sudo ss -tuln
```

---

## 🎯 Заключение

После выполнения всех шагов у вас будет полностью рабочий Sneakers API сервер с:

- ✅ Безопасной конфигурацией
- ✅ SSL сертификатом
- ✅ CORS поддержкой
- ✅ Автоматическими обновлениями
- ✅ Мониторингом и логированием

**Тестовые URL:**
- API: `https://your-domain.com/api/test-sneakers`
- Главная: `https://your-domain.com`

**Для обновления используйте:**
```bash
cd /var/www/sneakers-api && bash update.sh
```

Удачного развертывания! 🚀 
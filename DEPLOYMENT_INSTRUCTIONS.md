# Инструкция по развертыванию Laravel API на удаленном сервере

## Требования к серверу

### Минимальные системные требования:
- Ubuntu 20.04 LTS или выше / CentOS 8+ / Debian 10+
- 2 GB RAM (рекомендуется 4 GB)
- 20 GB свободного места на диске
- PHP 8.1+
- MySQL 8.0+ или PostgreSQL 13+
- Nginx или Apache
- Composer
- Node.js 16+ (для сборки ассетов)

## Шаг 1: Подготовка сервера

### 1.1 Обновление системы
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Установка необходимых пакетов
```bash
sudo apt install -y software-properties-common curl wget unzip git
```

## Шаг 2: Установка PHP и расширений

### 2.1 Добавление репозитория PHP
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

### 2.2 Установка PHP и расширений
```bash
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-bcmath php8.2-json php8.2-gd php8.2-intl \
php8.2-sqlite3 php8.2-tokenizer php8.2-fileinfo
```

### 2.3 Проверка установки PHP
```bash
php -v
```

## Шаг 3: Установка Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer --version
```

## Шаг 4: Установка и настройка базы данных

### 4.1 Установка MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 4.2 Создание базы данных и пользователя
```bash
sudo mysql -u root -p
```

В MySQL консоли:
```sql
CREATE DATABASE sneakers_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sneakers_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON sneakers_db.* TO 'sneakers_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Шаг 5: Установка и настройка Nginx

### 5.1 Установка Nginx
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 5.2 Создание конфигурации виртуального хоста
Создайте файл `/etc/nginx/sites-available/sneakers-api`:

```nginx
server {
    listen 80;
    server_name your-domain.com; # Замените на ваш домен или IP
    root /var/www/sneakers-api/public;
    index index.php index.html;

    # Максимальный размер загружаемых файлов
    client_max_body_size 10M;

    # Логи
    access_log /var/log/nginx/sneakers-api.access.log;
    error_log /var/log/nginx/sneakers-api.error.log;

    # Основная конфигурация для Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # API маршруты
    location ~ ^/api {
        try_files $uri $uri/ /index.php?$query_string;
        
        # CORS заголовки
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
        add_header Access-Control-Allow-Headers "Content-Type, Accept, Authorization, X-Requested-With, X-XSRF-TOKEN";
        
        # Обработка preflight запросов
        if ($request_method = OPTIONS) {
            add_header Access-Control-Allow-Origin *;
            add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
            add_header Access-Control-Allow-Headers "Content-Type, Accept, Authorization, X-Requested-With, X-XSRF-TOKEN";
            add_header Content-Length 0;
            add_header Content-Type text/plain;
            return 200;
        }
    }

    # PHP обработка
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Дополнительные настройки безопасности
        fastcgi_hide_header X-Powered-By;
    }

    # Запрет доступа к скрытым файлам
    location ~ /\. {
        deny all;
    }

    # Статические файлы
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 5.3 Активация сайта
```bash
sudo ln -s /etc/nginx/sites-available/sneakers-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Шаг 6: Развертывание кода приложения

### 6.1 Создание директории и клонирование проекта
```bash
sudo mkdir -p /var/www/sneakers-api
cd /var/www/sneakers-api

# Если используете Git
sudo git clone https://github.com/your-username/sneakers-api.git .

# Или загрузите файлы через SCP/SFTP
```

### 6.2 Установка зависимостей
```bash
sudo composer install --no-dev --optimize-autoloader
```

### 6.3 Настройка прав доступа
```bash
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage
sudo chmod -R 775 /var/www/sneakers-api/bootstrap/cache
```

## Шаг 7: Конфигурация Laravel

### 7.1 Создание файла .env
```bash
sudo cp .env.example .env
sudo nano .env
```

Содержимое файла `.env`:
```env
APP_NAME="Sneakers API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sneakers_db
DB_USERNAME=sneakers_user
DB_PASSWORD=your_strong_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Настройки для загрузки файлов
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
```

### 7.2 Генерация ключа приложения
```bash
sudo php artisan key:generate
```

### 7.3 Кеширование конфигурации
```bash
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### 7.4 Создание символической ссылки для storage
```bash
sudo php artisan storage:link
```

### 7.5 Выполнение миграций
```bash
sudo php artisan migrate --force
```

### 7.6 Заполнение базы данных (если есть сидеры)
```bash
sudo php artisan db:seed --force
```

## Шаг 8: Настройка SSL (рекомендуется)

### 8.1 Установка Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 8.2 Получение SSL сертификата
```bash
sudo certbot --nginx -d your-domain.com
```

## Шаг 9: Настройка мониторинга и логов

### 9.1 Настройка ротации логов
Создайте файл `/etc/logrotate.d/laravel`:
```
/var/www/sneakers-api/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### 9.2 Настройка cron для Laravel Scheduler (если используется)
```bash
sudo crontab -e
```
Добавьте строку:
```
* * * * * cd /var/www/sneakers-api && php artisan schedule:run >> /dev/null 2>&1
```

## Шаг 10: Настройка файрвола

```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw status
```

## Шаг 11: Оптимизация производительности

### 11.1 Настройка PHP-FPM
Отредактируйте файл `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000
```

### 11.2 Перезапуск служб
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

## Шаг 12: Тестирование API

### 12.1 Проверка работоспособности
```bash
curl -H "Accept: application/json" http://your-domain.com/api/test-sneakers
```

### 12.2 Проверка доступности изображений
```bash
curl -I http://your-domain.com/storage/images/sneakers/image5-1.jpg
```

## Шаг 13: Настройка для работы с фронтендом

### 13.1 Обновление CORS настроек
В файле `config/cors.php` убедитесь, что настройки позволяют запросы с вашего фронтенд домена:

```php
'allowed_origins' => ['https://your-frontend-domain.com'],
```

### 13.2 Настройка переменных окружения для фронтенда
На фронтенде используйте:
```
REACT_APP_API_URL=https://your-domain.com/api
# или для Vue.js
VITE_API_URL=https://your-domain.com/api
```

## Шаг 14: Резервное копирование

### 14.1 Скрипт для бэкапа базы данных
Создайте файл `/home/backup_db.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u sneakers_user -p'your_strong_password' sneakers_db > /backups/sneakers_db_$DATE.sql
find /backups -name "sneakers_db_*.sql" -mtime +7 -delete
```

### 14.2 Автоматический бэкап
```bash
sudo mkdir -p /backups
sudo chmod +x /home/backup_db.sh
sudo crontab -e
```
Добавьте:
```
0 2 * * * /home/backup_db.sh
```

## Устранение возможных проблем

### Проблема с правами доступа
```bash
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage /var/www/sneakers-api/bootstrap/cache
```

### Проблемы с CORS
Убедитесь, что в Nginx конфигурации правильно настроены CORS заголовки и в Laravel конфигурации `config/cors.php` указан правильный домен фронтенда.

### Проблемы с изображениями
```bash
sudo php artisan storage:link
sudo chmod -R 755 /var/www/sneakers-api/storage/app/public
```

## Мониторинг и обслуживание

- Регулярно проверяйте логи: `/var/log/nginx/` и `/var/www/sneakers-api/storage/logs/`
- Обновляйте систему и пакеты
- Мониторьте использование дискового пространства
- Проверяйте работоспособность API и базы данных

После выполнения всех шагов ваш Laravel API будет готов к работе на продакшн сервере! 
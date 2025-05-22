#!/bin/bash

# Скрипт автоматического развертывания Laravel API
# Использование: bash deploy.sh

set -e

echo "🚀 Начинаем развертывание Sneakers API..."

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Функция для вывода сообщений
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Проверка прав sudo
if [ "$EUID" -ne 0 ]; then
    error "Пожалуйста, запустите скрипт с правами sudo: sudo bash deploy.sh"
fi

# Функция для проверки статуса команды
check_command() {
    if ! command -v $1 &> /dev/null; then
        return 1
    fi
    return 0
}

# Ввод конфигурационных данных
read -p "Введите доменное имя или IP адрес сервера: " DOMAIN
read -p "Введите имя базы данных [sneakers_db]: " DB_NAME
DB_NAME=${DB_NAME:-sneakers_db}

read -p "Введите имя пользователя базы данных [sneakers_user]: " DB_USER
DB_USER=${DB_USER:-sneakers_user}

read -sp "Введите пароль для пользователя базы данных: " DB_PASSWORD
echo

read -sp "Введите пароль root для MySQL: " MYSQL_ROOT_PASSWORD
echo

# Обновление системы
log "Обновление системы..."
apt update && apt upgrade -y

# Установка необходимых пакетов
log "Установка необходимых пакетов..."
apt install -y software-properties-common curl wget unzip git

# Установка PHP 8.2 и расширений
log "Установка PHP 8.2 и расширений..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-bcmath php8.2-json php8.2-gd php8.2-intl \
php8.2-sqlite3 php8.2-tokenizer php8.2-fileinfo

# Проверка установки PHP
if check_command php; then
    log "PHP установлен: $(php -v | head -n1)"
else
    error "Ошибка установки PHP"
fi

# Установка Composer
log "Установка Composer..."
if ! check_command composer; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi

log "Composer установлен: $(composer --version | head -n1)"

# Установка MySQL
log "Установка MySQL..."
if ! check_command mysql; then
    apt install -y mysql-server
    systemctl start mysql
    systemctl enable mysql
fi

# Создание базы данных и пользователя
log "Настройка базы данных..."
mysql -u root -p"$MYSQL_ROOT_PASSWORD" <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

# Установка Nginx
log "Установка и настройка Nginx..."
if ! check_command nginx; then
    apt install -y nginx
    systemctl start nginx
    systemctl enable nginx
fi

# Создание конфигурации Nginx
log "Создание конфигурации виртуального хоста..."
cat > /etc/nginx/sites-available/sneakers-api <<EOF
server {
    listen 80;
    server_name ${DOMAIN};
    root /var/www/sneakers-api/public;
    index index.php index.html;

    client_max_body_size 10M;

    access_log /var/log/nginx/sneakers-api.access.log;
    error_log /var/log/nginx/sneakers-api.error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ ^/api {
        try_files \$uri \$uri/ /index.php?\$query_string;
        
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
        add_header Access-Control-Allow-Headers "Content-Type, Accept, Authorization, X-Requested-With, X-XSRF-TOKEN";
        
        if (\$request_method = OPTIONS) {
            add_header Access-Control-Allow-Origin *;
            add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
            add_header Access-Control-Allow-Headers "Content-Type, Accept, Authorization, X-Requested-With, X-XSRF-TOKEN";
            add_header Content-Length 0;
            add_header Content-Type text/plain;
            return 200;
        }
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\. {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Активация сайта
ln -sf /etc/nginx/sites-available/sneakers-api /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# Создание директории проекта
log "Создание директории проекта..."
mkdir -p /var/www/sneakers-api
cd /var/www/sneakers-api

# Запрос способа получения кода
echo "Выберите способ развертывания кода:"
echo "1) Git клонирование (требуется URL репозитория)"
echo "2) Ручная загрузка (файлы должны быть предварительно загружены в /var/www/sneakers-api)"
read -p "Введите номер (1 или 2): " DEPLOY_METHOD

if [ "$DEPLOY_METHOD" = "1" ]; then
    read -p "Введите URL Git репозитория: " GIT_URL
    log "Клонирование репозитория..."
    git clone "$GIT_URL" .
elif [ "$DEPLOY_METHOD" = "2" ]; then
    log "Убедитесь, что файлы проекта находятся в /var/www/sneakers-api"
    read -p "Нажмите Enter для продолжения..."
else
    error "Неверный выбор. Введите 1 или 2."
fi

# Установка зависимостей
log "Установка зависимостей Composer..."
composer install --no-dev --optimize-autoloader

# Настройка прав доступа
log "Настройка прав доступа..."
chown -R www-data:www-data /var/www/sneakers-api
chmod -R 755 /var/www/sneakers-api
chmod -R 775 /var/www/sneakers-api/storage
chmod -R 775 /var/www/sneakers-api/bootstrap/cache

# Создание .env файла
log "Создание файла .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Настройка .env файла
cat > .env <<EOF
APP_NAME="Sneakers API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://${DOMAIN}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
EOF

# Генерация ключа приложения
log "Генерация ключа приложения..."
php artisan key:generate

# Настройка Laravel
log "Настройка Laravel..."
php artisan config:cache
php artisan route:cache
php artisan storage:link

# Выполнение миграций
log "Выполнение миграций базы данных..."
php artisan migrate --force

# Заполнение базы данных
if [ -f database/seeders/DatabaseSeeder.php ]; then
    log "Заполнение базы данных..."
    php artisan db:seed --force
fi

# Настройка файрвола
log "Настройка файрвола..."
ufw --force enable
ufw allow ssh
ufw allow 'Nginx Full'

# Настройка SSL (опционально)
read -p "Хотите ли вы настроить SSL сертификат? (y/n): " SETUP_SSL
if [ "$SETUP_SSL" = "y" ] || [ "$SETUP_SSL" = "Y" ]; then
    log "Установка Certbot..."
    apt install -y certbot python3-certbot-nginx
    log "Получение SSL сертификата..."
    certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --email admin@"$DOMAIN"
fi

# Перезапуск служб
log "Перезапуск служб..."
systemctl restart php8.2-fpm
systemctl restart nginx

# Проверка работоспособности
log "Тестирование API..."
sleep 5
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "Accept: application/json" "http://$DOMAIN/api/test-sneakers")

if [ "$HTTP_CODE" = "200" ]; then
    log "✅ API успешно развернут и работает!"
    log "🌐 URL API: http://$DOMAIN/api"
    log "🧪 Тестовый эндпоинт: http://$DOMAIN/api/test-sneakers"
else
    warning "API развернут, но тестовый запрос вернул код: $HTTP_CODE"
    warning "Проверьте логи: /var/log/nginx/sneakers-api.error.log"
    warning "И логи Laravel: /var/www/sneakers-api/storage/logs/laravel.log"
fi

log "🎉 Развертывание завершено!"
echo
echo "📋 Информация о развертывании:"
echo "   Домен: $DOMAIN"
echo "   Путь к проекту: /var/www/sneakers-api"
echo "   База данных: $DB_NAME"
echo "   Пользователь БД: $DB_USER"
echo "   Логи Nginx: /var/log/nginx/sneakers-api.access.log"
echo "   Логи Laravel: /var/www/sneakers-api/storage/logs/laravel.log"
echo
echo "🔗 Основные эндпоинты API:"
echo "   GET  http://$DOMAIN/api/sneakers - список кроссовок"
echo "   GET  http://$DOMAIN/api/brands - список брендов"
echo "   GET  http://$DOMAIN/api/categories - список категорий"
echo "   GET  http://$DOMAIN/api/test-sneakers - тестовые данные" 
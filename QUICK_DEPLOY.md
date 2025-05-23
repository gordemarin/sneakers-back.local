# ⚡ Быстрое развертывание Sneakers API

## 🚀 Автоматическое развертывание (одной командой)

```bash
# Загрузите и запустите автоматический скрипт развертывания
curl -fsSL https://raw.githubusercontent.com/gordemarin/sneakers-back.local/main/deploy.sh | bash
```

## 📝 Ручное развертывание (пошагово)

### 1. Подготовка сервера
```bash
# Обновление системы
sudo apt update && sudo apt upgrade -y

# Установка базовых пакетов
sudo apt install -y curl wget git unzip software-properties-common ufw
```

### 2. Установка LEMP
```bash
# Nginx
sudo apt install -y nginx
sudo systemctl start nginx && sudo systemctl enable nginx

# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y && sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-dom

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Настройка базы данных
```bash
sudo mysql -u root -p
```
```sql
CREATE DATABASE sneakers_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sneakers_user'@'localhost' IDENTIFIED BY 'ВАША_ПАРОЛЬ';
GRANT ALL PRIVILEGES ON sneakers_api.* TO 'sneakers_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Клонирование проекта
```bash
cd /var/www
sudo git clone https://github.com/gordemarin/sneakers-back.local.git sneakers-api
sudo chown -R www-data:www-data sneakers-api
cd sneakers-api
```

### 5. Настройка Laravel
```bash
# Установка зависимостей
sudo -u www-data composer install --no-dev --optimize-autoloader

# Настройка окружения
sudo cp .env.example .env
sudo nano .env  # Укажите данные БД

# Laravel команды
sudo -u www-data php artisan key:generate
sudo -u www-data php artisan migrate
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan storage:link

# Права доступа
sudo chmod -R 775 storage bootstrap/cache
```

### 6. Конфигурация Nginx
```bash
sudo nano /etc/nginx/sites-available/sneakers-api
```

Добавьте конфигурацию:
```nginx
server {
    listen 80;
    server_name ВАШ_ДОМЕН;
    root /var/www/sneakers-api/public;
    index index.php;

    # CORS
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
}
```

```bash
# Активация сайта
sudo ln -s /etc/nginx/sites-available/sneakers-api /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL сертификат
```bash
# Установка Certbot
sudo apt install -y certbot python3-certbot-nginx

# Получение SSL
sudo certbot --nginx -d ВАШ_ДОМЕН
```

## ✅ Проверка работы

```bash
# Проверка API
curl -X GET https://ВАШ_ДОМЕН/api/test-sneakers

# Проверка служб
sudo systemctl status nginx php8.2-fpm mysql
```

## 🔄 Обновление

```bash
cd /var/www/sneakers-api
bash update.sh
```

## 🆘 Проблемы?

**500 ошибка:**
```bash
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage
sudo -u www-data php artisan config:clear
```

**База данных:**
```bash
# Проверьте .env файл и подключение к MySQL
sudo nano /var/www/sneakers-api/.env
mysql -u sneakers_user -p sneakers_api
```

**CORS:**
```bash
# Проверьте конфигурацию Nginx
sudo nginx -t
sudo systemctl reload nginx
```

## 📚 Полная документация

- **Полная инструкция:** `COMPLETE_DEPLOYMENT_GUIDE.md`
- **Исправление проблем:** `GIT_PERMISSION_FIX.md`
- **Интеграция с фронтендом:** `FRONTEND_INTEGRATION.md`

---

**Репозиторий:** https://github.com/gordemarin/sneakers-back.local  
**Время развертывания:** ~15-30 минут  
**Поддержка:** Ubuntu 20.04+, PHP 8.2+, MySQL 8.0+ 
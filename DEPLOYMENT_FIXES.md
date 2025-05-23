# Исправление проблем с развертыванием

## Проблема с пакетом php8.2-json

### Описание ошибки:
```
Package 'php8.2-json' has no installation candidate
```

### Причина:
В некоторых дистрибутивах Linux поддержка JSON встроена в основной пакет PHP и не требует отдельной установки.

### Решение:

#### Для Ubuntu/Debian:
```bash
# Вместо php8.2-json используйте:
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl \
php8.2-sqlite3 php8.2-tokenizer php8.2-fileinfo php8.2-dom
```

#### Для CentOS/RHEL/Rocky Linux:
```bash
# Установка репозитория Remi
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# Включение модуля PHP 8.2
sudo dnf module reset php
sudo dnf module enable php:remi-8.2

# Установка PHP и расширений
sudo dnf install -y php php-fpm php-mysqlnd php-xml php-mbstring \
php-curl php-zip php-bcmath php-gd php-intl php-sqlite3 \
php-tokenizer php-fileinfo php-dom php-json
```

#### Для Alpine Linux:
```bash
# Установка PHP и расширений
apk add --no-cache php82 php82-fpm php82-mysqli php82-xml php82-mbstring \
php82-curl php82-zip php82-bcmath php82-gd php82-intl php82-sqlite3 \
php82-tokenizer php82-fileinfo php82-dom php82-json
```

## Альтернативный скрипт для CentOS/RHEL

Создайте файл `deploy-centos.sh`:

```bash
#!/bin/bash

# Скрипт развертывания для CentOS/RHEL/Rocky Linux

set -e

echo "🚀 Начинаем развертывание Sneakers API на CentOS/RHEL..."

# Обновление системы
sudo dnf update -y

# Установка необходимых пакетов
sudo dnf install -y curl wget unzip git

# Установка репозитория Remi для PHP
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# Включение модуля PHP 8.2
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.2 -y

# Установка PHP и расширений
sudo dnf install -y php php-fpm php-mysqlnd php-xml php-mbstring \
php-curl php-zip php-bcmath php-gd php-intl php-sqlite3 \
php-tokenizer php-fileinfo php-dom php-json

# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Установка MySQL
sudo dnf install -y mysql-server
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Установка Nginx
sudo dnf install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Продолжение установки аналогично основному скрипту...
```

## Проверка установленных расширений PHP

После установки проверьте, что все необходимые расширения установлены:

```bash
php -m | grep -E "(json|curl|zip|mbstring|xml|mysql|gd|bcmath|intl|tokenizer|fileinfo|dom)"
```

Вы должны увидеть все эти расширения в списке.

## Альтернативная установка через Composer

Если возникают проблемы с системными пакетами, можно установить некоторые расширения через Composer:

```bash
# В директории проекта
composer require ext-json ext-curl ext-zip ext-mbstring ext-xml
```

## Проблемы с правами доступа

Если возникают ошибки с правами доступа:

```bash
# Для Ubuntu/Debian
sudo chown -R www-data:www-data /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage /var/www/sneakers-api/bootstrap/cache

# Для CentOS/RHEL
sudo chown -R nginx:nginx /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage /var/www/sneakers-api/bootstrap/cache

# Для Alpine
sudo chown -R nginx:nginx /var/www/sneakers-api
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage /var/www/sneakers-api/bootstrap/cache
```

## Проблемы с SELinux (CentOS/RHEL)

Если используется SELinux, может потребоваться настройка:

```bash
# Проверка статуса SELinux
sestatus

# Временное отключение SELinux
sudo setenforce 0

# Или настройка правил для веб-сервера
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_execmem 1
sudo chcon -R -t httpd_exec_t /var/www/sneakers-api
```

## Ручная установка без скрипта

Если автоматический скрипт не работает, выполните установку вручную по шагам из `DEPLOYMENT_INSTRUCTIONS.md`.

## Проверка логов

При возникновении проблем проверьте логи:

```bash
# Логи системы
sudo journalctl -u php8.2-fpm -n 50
sudo journalctl -u nginx -n 50

# Логи веб-сервера
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/sneakers-api.error.log

# Логи PHP
sudo tail -f /var/log/php8.2-fpm.log

# Логи Laravel
sudo tail -f /var/www/sneakers-api/storage/logs/laravel.log
```

## Контакты для поддержки

Если проблемы не решаются:
1. Укажите версию вашего дистрибутива: `cat /etc/os-release`
2. Приложите полный вывод ошибки
3. Проверьте логи указанными выше командами 
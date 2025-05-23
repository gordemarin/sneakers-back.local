# Исправление проблем с правами доступа Git

## Ошибка: Permission denied при работе с Git

### Описание проблемы:
```bash
$ git add .
fatal: Unable to create '/var/www/sneakers-api/.git/index.lock': Permission denied
```

### Причины:
1. Файлы принадлежат root, а Git выполняется от обычного пользователя
2. Остался заблокированный файл `index.lock` от предыдущей операции
3. Неправильные права доступа на директорию `.git`

## 🔧 Решения

### Решение 1: Исправление прав доступа (рекомендуется)

```bash
# Перейдите в директорию проекта
cd /var/www/sneakers-api

# Измените владельца всех файлов на текущего пользователя
sudo chown -R $USER:$USER .

# Или если нужно изменить на конкретного пользователя
sudo chown -R username:username .

# Установите правильные права доступа
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Решение 2: Удаление заблокированного файла

```bash
# Удалите файл блокировки если он существует
sudo rm -f /var/www/sneakers-api/.git/index.lock

# Затем попробуйте снова
git add .
```

### Решение 3: Выполнение Git команд с sudo

```bash
# Если предыдущие решения не помогли, используйте sudo
sudo git add .
sudo git commit -m "Update files"
sudo git push origin main

# Но после этого обязательно исправьте права доступа:
sudo chown -R $USER:$USER .
```

### Решение 4: Переинициализация Git с правильными правами

```bash
# Если проблема критическая, переинициализируйте Git
cd /var/www/sneakers-api

# Удалите .git директорию (ОСТОРОЖНО! Потеряете историю коммитов)
sudo rm -rf .git

# Инициализируйте заново
git init
git remote add origin https://github.com/YOUR_USERNAME/sneakers-api.git

# Добавьте файлы и сделайте коммит
git add .
git commit -m "Reinitialize repository"
git branch -M main
git push -u origin main
```

## 🚀 Настройка правильного рабочего процесса

### Для постоянного решения проблемы создайте пользователя для развертывания:

```bash
# Создайте пользователя для развертывания
sudo adduser deploy
sudo usermod -aG www-data deploy

# Измените владельца директории проекта
sudo chown -R deploy:www-data /var/www/sneakers-api

# Установите правильные права доступа
sudo chmod -R 755 /var/www/sneakers-api
sudo chmod -R 775 /var/www/sneakers-api/storage
sudo chmod -R 775 /var/www/sneakers-api/bootstrap/cache

# Переключитесь на пользователя deploy
sudo su - deploy
cd /var/www/sneakers-api

# Теперь Git команды будут работать без sudo
git pull origin main
```

### Настройка SSH ключей для пользователя deploy:

```bash
# Под пользователем deploy
ssh-keygen -t rsa -b 4096 -C "deploy@your-server.com"

# Добавьте публичный ключ в GitHub
cat ~/.ssh/id_rsa.pub
# Скопируйте вывод и добавьте в GitHub: Settings > SSH and GPG keys

# Теперь можно клонировать через SSH
git remote set-url origin git@github.com:YOUR_USERNAME/sneakers-api.git
```

## 📝 Скрипт автоматического обновления

Создайте файл `update.sh` для безопасного обновления:

```bash
#!/bin/bash

# Скрипт обновления проекта на сервере
# Использование: bash update.sh

set -e

echo "🔄 Начинаем обновление Sneakers API..."

# Проверяем, что мы в правильной директории
if [ ! -f "artisan" ]; then
    echo "❌ Ошибка: Запустите скрипт из корневой директории Laravel проекта"
    exit 1
fi

# Проверяем права доступа
if [ ! -w ".git" ]; then
    echo "⚠️  Исправляем права доступа..."
    sudo chown -R $USER:$USER .
    chmod -R 755 .
    chmod -R 775 storage bootstrap/cache
fi

# Сохраняем изменения (если есть)
if ! git diff --quiet || ! git diff --cached --quiet; then
    echo "💾 Сохраняем локальные изменения..."
    git add .
    git commit -m "Auto-save before update: $(date)"
fi

# Получаем обновления
echo "📥 Получаем обновления с GitHub..."
git pull origin main

# Обновляем зависимости
echo "📦 Обновляем зависимости..."
composer install --no-dev --optimize-autoloader

# Выполняем миграции
echo "🗄️ Выполняем миграции..."
php artisan migrate --force

# Очищаем кеши
echo "🧹 Очищаем кеши..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Перезапускаем службы
echo "🔄 Перезапускаем службы..."
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

echo "✅ Обновление завершено успешно!"
```

Сделайте скрипт исполняемым:
```bash
chmod +x update.sh
```

## 🔐 Безопасные практики

### 1. Никогда не используйте root для Git операций
```bash
# ПЛОХО
sudo git pull

# ХОРОШО
# Настройте правильного пользователя и используйте:
git pull
```

### 2. Используйте отдельного пользователя для развертывания
```bash
# Создайте пользователя deploy
sudo adduser deploy
sudo usermod -aG www-data deploy

# Работайте под этим пользователем
sudo su - deploy
```

### 3. Настройте правильные права доступа
```bash
# Владелец: deploy, группа: www-data
sudo chown -R deploy:www-data /var/www/sneakers-api

# Права: 755 для файлов, 775 для директорий с записью
sudo find /var/www/sneakers-api -type f -exec chmod 644 {} \;
sudo find /var/www/sneakers-api -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/sneakers-api/storage
sudo chmod -R 775 /var/www/sneakers-api/bootstrap/cache
```

## ❓ Диагностика проблем

### Проверьте права доступа:
```bash
ls -la /var/www/sneakers-api/
ls -la /var/www/sneakers-api/.git/
```

### Проверьте владельца файлов:
```bash
stat /var/www/sneakers-api/.git/
whoami
```

### Проверьте заблокированные файлы:
```bash
find /var/www/sneakers-api/.git/ -name "*.lock"
```

## 🆘 Экстренное восстановление

Если Git репозиторий сильно поврежден:

```bash
# 1. Сделайте бэкап текущего кода
sudo cp -r /var/www/sneakers-api /backup/sneakers-api-$(date +%Y%m%d)

# 2. Заново клонируйте репозиторий
sudo rm -rf /var/www/sneakers-api
sudo git clone https://github.com/YOUR_USERNAME/sneakers-api.git /var/www/sneakers-api

# 3. Восстановите настройки
sudo chown -R deploy:www-data /var/www/sneakers-api
cd /var/www/sneakers-api
cp /backup/sneakers-api-*/env .env

# 4. Запустите обновление
bash update.sh
```

Выберите подходящее решение в зависимости от вашей ситуации! 
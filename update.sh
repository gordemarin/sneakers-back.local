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

# Удаляем заблокированные файлы Git если они есть
if [ -f ".git/index.lock" ]; then
    echo "🔓 Удаляем заблокированный файл Git..."
    rm -f .git/index.lock
fi

# Сохраняем изменения (если есть)
if ! git diff --quiet 2>/dev/null || ! git diff --cached --quiet 2>/dev/null; then
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

# Устанавливаем правильные права доступа
echo "🔒 Устанавливаем права доступа..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# Перезапускаем службы
echo "🔄 Перезапускаем службы..."
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

echo "✅ Обновление завершено успешно!"
echo "🌐 Проверьте работу API: http://your-domain.com/api/test-sneakers" 
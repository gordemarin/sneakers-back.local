#!/bin/bash

echo "📦 Создание пакета для развертывания Sneakers API..."

# Проверяем, что мы в правильной директории
if [ ! -f "artisan" ]; then
    echo "❌ Ошибка: Запустите скрипт из корневой директории Laravel проекта"
    exit 1
fi

# Создаем временную директорию
TEMP_DIR="sneakers-api-package"
mkdir -p "$TEMP_DIR"

echo "📋 Копирование файлов..."

# Копируем все файлы кроме ненужных
rsync -av \
    --exclude='node_modules/' \
    --exclude='.git/' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='vendor/' \
    --exclude='.env' \
    --exclude='*.zip' \
    --exclude='*.tar.gz' \
    ./ "$TEMP_DIR/"

# Создаем необходимые пустые директории
mkdir -p "$TEMP_DIR/storage/logs"
mkdir -p "$TEMP_DIR/storage/framework/cache/data"
mkdir -p "$TEMP_DIR/storage/framework/sessions"
mkdir -p "$TEMP_DIR/storage/framework/views"
mkdir -p "$TEMP_DIR/bootstrap/cache"

# Устанавливаем правильные права доступа
chmod -R 755 "$TEMP_DIR"
chmod -R 775 "$TEMP_DIR/storage"
chmod -R 775 "$TEMP_DIR/bootstrap/cache"

# Создаем файл с информацией о пакете
cat > "$TEMP_DIR/PACKAGE_INFO.txt" <<EOF
Sneakers API Deployment Package
===============================

Дата создания: $(date)
Версия Laravel: $(grep '"laravel/framework"' composer.json | sed 's/.*: *"\([^"]*\)".*/\1/')

Инструкции по развертыванию:
1. Распакуйте архив в /var/www/sneakers-api/
2. Запустите: sudo bash deploy.sh
3. Следуйте инструкциям скрипта развертывания

Основные эндпоинты API:
- GET /api/sneakers - список кроссовок
- GET /api/brands - список брендов  
- GET /api/categories - список категорий
- GET /api/test-sneakers - тестовые данные

Дополнительная документация:
- DEPLOYMENT_INSTRUCTIONS.md - подробная инструкция по развертыванию
- QUICK_START.md - быстрый старт
- FRONTEND_INTEGRATION.md - интеграция с фронтендом
EOF

# Создаем архив
ARCHIVE_NAME="sneakers-api-$(date +%Y%m%d-%H%M%S).tar.gz"
echo "📦 Создание архива: $ARCHIVE_NAME"

tar -czf "$ARCHIVE_NAME" -C "$TEMP_DIR" .

# Удаляем временную директорию
rm -rf "$TEMP_DIR"

# Показываем информацию о созданном пакете
ARCHIVE_SIZE=$(du -h "$ARCHIVE_NAME" | cut -f1)
echo ""
echo "✅ Пакет успешно создан!"
echo "📁 Файл: $ARCHIVE_NAME"
echo "📏 Размер: $ARCHIVE_SIZE"
echo ""
echo "🚀 Способы загрузки на сервер:"
echo ""
echo "1️⃣  Через SCP:"
echo "    scp $ARCHIVE_NAME username@your-server-ip:/home/username/"
echo ""
echo "2️⃣  Через веб-панель хостинга:"
echo "    Загрузите файл через файловый менеджер"
echo ""
echo "3️⃣  На сервере распакуйте:"
echo "    sudo mkdir -p /var/www/sneakers-api"
echo "    sudo tar -xzf $ARCHIVE_NAME -C /var/www/sneakers-api"
echo "    cd /var/www/sneakers-api"
echo "    sudo bash deploy.sh"
echo ""
echo "💡 Рекомендуем использовать GitHub для более удобного управления кодом!" 
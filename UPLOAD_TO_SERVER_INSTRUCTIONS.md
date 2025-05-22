# Инструкция по загрузке кода на виртуальную машину

## Способ 1: Через GitHub (рекомендуется)

### Подготовка на локальной машине

1. **Инициализация Git репозитория (если еще не сделано):**
```bash
cd /d/OSPanel/home/sneakers-back.local
git init
git add .
git commit -m "Initial commit: Laravel Sneakers API"
```

2. **Создание репозитория на GitHub:**
- Зайдите на https://github.com
- Нажмите "New repository"
- Назовите репозиторий, например: `sneakers-api`
- Сделайте репозиторий публичным или приватным (по желанию)
- НЕ инициализируйте с README, .gitignore или лицензией

3. **Связывание локального репозитория с GitHub:**
```bash
git remote add origin https://github.com/ваш-username/sneakers-api.git
git branch -M main
git push -u origin main
```

### Развертывание на сервере

1. **Подключение к серверу по SSH:**
```bash
ssh username@your-server-ip
```

2. **Клонирование репозитория:**
```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/ваш-username/sneakers-api.git sneakers-api
cd sneakers-api
```

3. **Запуск автоматического развертывания:**
```bash
sudo bash deploy.sh
```

### Обновление кода на сервере

Когда вы внесете изменения локально:

**На локальной машине:**
```bash
git add .
git commit -m "Описание изменений"
git push origin main
```

**На сервере:**
```bash
cd /var/www/sneakers-api
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## Способ 2: Через SCP (прямая передача файлов)

### С Windows (через PowerShell или WSL)

1. **Архивирование проекта:**
```bash
# В PowerShell
Compress-Archive -Path "D:\OSPanel\home\sneakers-back.local\*" -DestinationPath "sneakers-api.zip"
```

2. **Передача файла на сервер:**
```bash
scp sneakers-api.zip username@your-server-ip:/home/username/
```

3. **На сервере:**
```bash
ssh username@your-server-ip
sudo mkdir -p /var/www/sneakers-api
cd /home/username
unzip sneakers-api.zip -d /tmp/sneakers-api/
sudo cp -r /tmp/sneakers-api/* /var/www/sneakers-api/
sudo rm -rf /tmp/sneakers-api
rm sneakers-api.zip
cd /var/www/sneakers-api
sudo bash deploy.sh
```

## Способ 3: Через rsync (синхронизация)

```bash
# С локальной машины (через WSL или Git Bash)
rsync -avz --exclude 'node_modules' --exclude '.git' \
  /d/OSPanel/home/sneakers-back.local/ \
  username@your-server-ip:/tmp/sneakers-api/

# На сервере
ssh username@your-server-ip
sudo mkdir -p /var/www/sneakers-api
sudo cp -r /tmp/sneakers-api/* /var/www/sneakers-api/
sudo rm -rf /tmp/sneakers-api
cd /var/www/sneakers-api
sudo bash deploy.sh
```

## Способ 4: Создание установочного архива

### Подготовка архива для развертывания

Создам скрипт для упаковки проекта:

```bash
# create-package.sh
#!/bin/bash

echo "📦 Создание пакета для развертывания..."

# Создаем временную директорию
mkdir -p dist
cd dist

# Копируем все файлы кроме ненужных
rsync -av --exclude='node_modules' \
  --exclude='.git' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  ../ ./

# Создаем архив
tar -czf ../sneakers-api-deploy.tar.gz .

cd ..
rm -rf dist

echo "✅ Пакет создан: sneakers-api-deploy.tar.gz"
echo "📤 Загрузите файл на сервер и распакуйте:"
echo "   tar -xzf sneakers-api-deploy.tar.gz -C /var/www/sneakers-api"
```

## Способ 5: Через файловый менеджер хостинга

Если у вас есть веб-панель управления хостингом:

1. **Архивирование проекта** в ZIP
2. **Загрузка через файловый менеджер** панели управления
3. **Распаковка** в нужную директорию
4. **Подключение по SSH** для выполнения команд развертывания

## Настройка SSH ключей (для удобства)

### Генерация SSH ключа на локальной машине:

```bash
# В Git Bash или WSL
ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
cat ~/.ssh/id_rsa.pub
```

### Добавление ключа на сервер:

```bash
# На сервере
mkdir -p ~/.ssh
echo "ваш-публичный-ключ" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
```

Теперь вы сможете подключаться без пароля!

## Автоматизация развертывания через GitHub Actions (продвинутый уровень)

### Создание файла `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Server

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/sneakers-api
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          systemctl restart php8.2-fpm
          systemctl reload nginx
```

### Настройка Secrets в GitHub:
- `HOST` - IP адрес сервера
- `USERNAME` - имя пользователя SSH
- `SSH_KEY` - приватный SSH ключ

## Структура .gitignore

Убедитесь, что у вас правильный `.gitignore`:

```gitignore
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
/storage/logs/*.log
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
```

## Проверочный список перед развертыванием

- [ ] Код загружен в Git репозиторий
- [ ] `.env.example` файл присутствует
- [ ] Все зависимости указаны в `composer.json`
- [ ] Миграции созданы и протестированы
- [ ] Изображения находятся в правильных директориях
- [ ] Скрипт `deploy.sh` имеет права на выполнение
- [ ] SSH доступ к серверу настроен
- [ ] Домен или IP адрес сервера известен

## Быстрый старт с GitHub

```bash
# 1. На локальной машине
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/ваш-username/sneakers-api.git
git push -u origin main

# 2. На сервере
ssh username@your-server-ip
sudo git clone https://github.com/ваш-username/sneakers-api.git /var/www/sneakers-api
cd /var/www/sneakers-api
sudo bash deploy.sh
```

GitHub - это самый удобный и надежный способ для управления кодом и развертывания на сервере! 
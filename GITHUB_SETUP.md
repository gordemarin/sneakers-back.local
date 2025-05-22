# Пошаговая инструкция по загрузке проекта на GitHub

## ✅ Что уже сделано:
- Git репозиторий инициализирован
- Все файлы добавлены в Git
- Создан первый коммит

## 🚀 Следующие шаги:

### 1. Создание репозитория на GitHub

1. **Перейдите на https://github.com**
2. **Войдите в свой аккаунт** (или создайте новый)
3. **Нажмите кнопку "New"** (зеленая кнопка) или "+" в правом верхнем углу
4. **Выберите "New repository"**

### 2. Настройка репозитория

**Заполните форму:**
- **Repository name:** `sneakers-api` (или любое другое название)
- **Description:** `Laravel API for sneakers e-commerce with React frontend`
- **Visibility:** 
  - ✅ **Public** (если хотите, чтобы код был виден всем)
  - ✅ **Private** (если хотите ограничить доступ)

**ВАЖНО: НЕ ставьте галочки на:**
- ❌ Add a README file
- ❌ Add .gitignore  
- ❌ Choose a license

### 3. Создание репозитория

**Нажмите "Create repository"**

### 4. Подключение локального репозитория

После создания репозитория GitHub покажет инструкции. Выполните команды:

```bash
# Замените YOUR_USERNAME на ваш GitHub username
git remote add origin https://github.com/YOUR_USERNAME/sneakers-api.git

# Переименуем ветку в main (современный стандарт)
git branch -M main

# Загружаем код на GitHub
git push -u origin main
```

## 📋 Готовые команды для копирования:

Откройте PowerShell в директории проекта и выполните:

```powershell
# 1. Добавляем удаленный репозиторий (замените YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/sneakers-api.git

# 2. Переименовываем ветку
git branch -M main

# 3. Загружаем код на GitHub
git push -u origin main
```

## 🔐 Если запросит авторизацию:

При первой загрузке Git может запросить логин и пароль:

**Логин:** ваш GitHub username
**Пароль:** 
- Если у вас включена двухфакторная аутентификация - используйте **Personal Access Token**
- Если нет 2FA - используйте обычный пароль

### Создание Personal Access Token (рекомендуется):

1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token (classic)
3. Выберите scopes: `repo` (полный доступ к репозиториям)
4. Скопируйте токен и используйте его вместо пароля

## ✅ После успешной загрузки:

Ваш проект будет доступен по адресу:
```
https://github.com/YOUR_USERNAME/sneakers-api
```

## 🖥️ Развертывание на сервере:

После загрузки на GitHub, на сервере выполните:

```bash
# Подключение к серверу
ssh username@your-server-ip

# Клонирование проекта
sudo git clone https://github.com/YOUR_USERNAME/sneakers-api.git /var/www/sneakers-api

# Переход в директорию
cd /var/www/sneakers-api

# Запуск автоматического развертывания
sudo bash deploy.sh
```

## 🔄 Обновление кода в будущем:

**На локальной машине (после внесения изменений):**
```bash
git add .
git commit -m "Описание изменений"
git push origin main
```

**На сервере (для получения обновлений):**
```bash
cd /var/www/sneakers-api
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo php artisan migrate --force
sudo php artisan config:cache
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## 🆘 Помощь:

Если что-то не получается:
1. Проверьте, что вы заменили `YOUR_USERNAME` на ваш реальный GitHub username
2. Убедитесь, что репозиторий создан правильно (без README, .gitignore, license)
3. Проверьте интернет-соединение
4. При проблемах с авторизацией используйте Personal Access Token

## 📞 Готово!

После выполнения всех шагов ваш код будет на GitHub и готов к развертыванию на любом сервере! 
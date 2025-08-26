# Развертывание Car Management API

## Production развертывание

### Системные требования

- **PHP:** 8.2 или выше
- **Composer:** 2.x
- **База данных:** MySQL 8.0+ / PostgreSQL 13+ / SQLite 3
- **Веб-сервер:** Nginx / Apache
- **SSL сертификат** для HTTPS

### Подготовка сервера

#### 1. Установка PHP и расширений
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-pgsql \
                 php8.2-sqlite3 php8.2-mbstring php8.2-xml \
                 php8.2-curl php8.2-zip php8.2-bcmath

# CentOS/RHEL
sudo yum install php82 php82-fpm php82-mysqlnd php82-pgsql \
                 php82-sqlite3 php82-mbstring php82-xml \
                 php82-curl php82-zip php82-bcmath
```

#### 2. Установка Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 3. Клонирование и настройка проекта
```bash
# Клонирование
git clone <repository-url> /var/www/car-api
cd /var/www/car-api

# Установка зависимостей
composer install --no-dev --optimize-autoloader

# Права доступа
sudo chown -R www-data:www-data /var/www/car-api
sudo chmod -R 755 /var/www/car-api
sudo chmod -R 775 /var/www/car-api/storage
sudo chmod -R 775 /var/www/car-api/bootstrap/cache
```

### Конфигурация

#### 1. Переменные окружения
```bash
cp .env.example .env
php artisan key:generate
```

Настройте `.env` файл:
```bash
APP_NAME="Car Management API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

# База данных
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_management
DB_USERNAME=car_user
DB_PASSWORD=strong_password

# Кеш и сессии
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Почта
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=mail_password
MAIL_ENCRYPTION=tls
```

#### 2. База данных
```bash
# Создание базы данных (MySQL)
mysql -u root -p
CREATE DATABASE car_management;
CREATE USER 'car_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON car_management.* TO 'car_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Миграции и сидеры
php artisan migrate --force
php artisan db:seed --force
```

#### 3. Кеширование и оптимизация
```bash
# Кеширование конфигурации
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Оптимизация автозагрузчика
composer dump-autoload --optimize

# Очистка кешей разработки
php artisan cache:clear
```

### Веб-сервер

#### Nginx конфигурация
```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.yourdomain.com;
    root /var/www/car-api/public;
    
    index index.php;
    
    # SSL сертификаты
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    
    # Безопасность
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    
    # API маршруты
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP обработка
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Запрет доступа к скрытым файлам
    location ~ /\. {
        deny all;
    }
    
    # Кеширование статики
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

#### Apache конфигурация
```apache
<VirtualHost *:80>
    ServerName api.yourdomain.com
    Redirect permanent / https://api.yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName api.yourdomain.com
    DocumentRoot /var/www/car-api/public
    
    # SSL настройки
    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/private.key
    
    # Безопасность
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    
    <Directory /var/www/car-api/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Логи
    ErrorLog ${APACHE_LOG_DIR}/car-api-error.log
    CustomLog ${APACHE_LOG_DIR}/car-api-access.log combined
</VirtualHost>
```

## Мониторинг и логирование

### 1. Настройка логов
```bash
# Конфигурация в config/logging.php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'slack'],
],

'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'debug',
    'days' => 14,
],
```

### 2. Мониторинг производительности
```bash
# Установка Laravel Telescope (только для staging)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Мониторинг здоровья
```bash
# Health check endpoint
GET /up

# Response
{
    "status": "ok",
    "timestamp": "2025-08-26T10:00:00Z"
}
```

## Безопасность

### 1. Firewall настройки
```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Закрыть прямой доступ к базе данных
sudo ufw deny 3306
sudo ufw deny 5432
```

### 2. Регулярные обновления
```bash
# Создать cron job для обновлений
0 2 * * 0 cd /var/www/car-api && composer update --no-dev --optimize-autoloader
```

### 3. Backup базы данных
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u car_user -p car_management > /backups/car_api_$DATE.sql
find /backups -name "car_api_*.sql" -mtime +7 -delete
```

## CI/CD Pipeline

### GitHub Actions пример
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run tests
      run: php artisan test
      
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/car-api
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl reload php8.2-fpm
          sudo systemctl reload nginx
```

## Решение проблем

### Проверка состояния
```bash
# Проверка PHP
php -v
php -m

# Проверка Laravel
php artisan --version
php artisan about

# Проверка базы данных
php artisan migrate:status

# Проверка очередей
php artisan queue:work --timeout=60
```

### Распространенные проблемы

#### 1. Ошибки прав доступа
```bash
sudo chown -R www-data:www-data /var/www/car-api
sudo chmod -R 755 /var/www/car-api
sudo chmod -R 775 /var/www/car-api/storage
sudo chmod -R 775 /var/www/car-api/bootstrap/cache
```

#### 2. Проблемы с базой данных
```bash
# Пересоздание миграций
php artisan migrate:fresh --force --seed
```

#### 3. Очистка кешей
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Production чеклист

- [ ] SSL сертификат установлен и валиден
- [ ] Firewall настроен и активен
- [ ] Базы данных настроены с правильными правами
- [ ] Резервное копирование настроено
- [ ] Логирование работает
- [ ] Мониторинг настроен
- [ ] Performance оптимизации применены
- [ ] Автоматические обновления безопасности
- [ ] Документация API обновлена
- [ ] Load testing выполнен

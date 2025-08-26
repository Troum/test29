# Документация Car Management API

Добро пожаловать в документацию Car Management API! Здесь собрана вся необходимая информация для работы с API.

## 📚 Структура документации

### Основные файлы

1. **[README.md](../README.md)** - Главная страница с описанием проекта
2. **[ARCHITECTURE.md](ARCHITECTURE.md)** - Детальное описание архитектуры
3. **[API_EXAMPLES.md](API_EXAMPLES.md)** - Примеры использования API
4. **[DEPLOYMENT.md](DEPLOYMENT.md)** - Инструкции по развертыванию

### Интерактивная документация

- **Swagger UI:** `/api/documentation` - интерактивная документация API
- **OpenAPI JSON:** `/api/documentation.json` - схема API в формате OpenAPI 3.0

## 🚀 Быстрый старт

### 1. Запуск проекта
```bash
# Клонирование и установка
git clone <repository-url>
cd api.test29
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Запуск сервера
php artisan serve
```

### 2. Доступ к документации
- Откройте браузер: http://localhost:8000/api/documentation
- Или используйте API Examples для тестирования через curl

### 3. Первые шаги
1. Зарегистрируйтесь: `POST /api/auth/register`
2. Получите токен авторизации
3. Создайте автомобиль: `POST /api/cars`
4. Изучите возможности совместного использования

## 📖 Как пользоваться документацией

### Swagger UI
1. Перейдите на `/api/documentation`
2. Нажмите "Authorize" и введите Bearer токен
3. Используйте "Try it out" для тестирования эндпоинтов
4. Изучайте схемы запросов и ответов

### Примеры кода
- В [API_EXAMPLES.md](API_EXAMPLES.md) найдете готовые curl команды
- Копируйте и адаптируйте под свои нужды
- Все примеры протестированы и работают

### Архитектурная документация
- [ARCHITECTURE.md](ARCHITECTURE.md) объясняет принципы построения
- Полезно для понимания кодовой базы
- Содержит диаграммы и примеры кода

## 🔍 Поиск информации

### По функциональности
- **Аутентификация:** Swagger UI → Authentication tag
- **Управление авто:** Swagger UI → Cars tag  
- **Справочники:** Swagger UI → Car Brands, Car Models tags

### По ошибкам
- **Коды ошибок:** [API_EXAMPLES.md](API_EXAMPLES.md#обработка-ошибок)
- **Решение проблем:** [DEPLOYMENT.md](DEPLOYMENT.md#решение-проблем)

### По архитектуре
- **Паттерны:** [ARCHITECTURE.md](ARCHITECTURE.md#паттерны-проектирования)
- **Слои:** [ARCHITECTURE.md](ARCHITECTURE.md#слои-архитектуры)
- **Принципы:** [ARCHITECTURE.md](ARCHITECTURE.md#принципы-solid)

## 🛠 Для разработчиков

### Обновление документации
```bash
# Регенерация OpenAPI
php artisan l5-swagger:generate

# Запуск тестов
php artisan test

# Проверка стиля кода
./vendor/bin/pint --test
```

### Добавление новых эндпоинтов
1. Добавьте OpenAPI аннотации к контроллеру
2. Обновите примеры в API_EXAMPLES.md
3. Регенерируйте документацию
4. Протестируйте через Swagger UI

### Структура OpenAPI аннотаций
```php
/**
 * @OA\Post(
 *     path="/api/endpoint",
 *     tags={"Tag Name"},
 *     summary="Краткое описание",
 *     description="Подробное описание",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(ref="#/components/schemas/RequestSchema"),
 *     @OA\Response(response=200, description="Успех"),
 *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
 * )
 */
```

## 📊 Метрики и мониторинг

### Доступные эндпоинты мониторинга
- `GET /up` - health check
- Laravel Telescope (в режиме разработки)
- Логи в `storage/logs/laravel.log`

### Производительность
- Все запросы логируются
- SQL запросы оптимизированы с Eager Loading
- Используется кеширование для справочников

## 🤝 Обратная связь

### Сообщить об ошибке
1. Проверьте [известные проблемы](DEPLOYMENT.md#решение-проблем)
2. Создайте issue с подробным описанием
3. Приложите логи и примеры запросов

### Предложить улучшение
1. Изучите [архитектуру](ARCHITECTURE.md)
2. Создайте feature request
3. Следуйте принципам SOLID при реализации

## 📄 Лицензия

Проект распространяется под лицензией MIT. Подробности в файле [LICENSE](../LICENSE).

---

**Последнее обновление:** 26 августа 2025  
**Версия API:** 1.0.0  
**Версия документации:** 1.0.0

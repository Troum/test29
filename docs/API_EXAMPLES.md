# API Examples

Примеры использования Car Management API.

## Аутентификация

### Регистрация пользователя
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Иван Иванов",
    "email": "ivan@example.com",
    "password": "password123"
  }'
```

**Ответ:**
```json
{
  "success": true,
  "message": "Регистрация прошла успешно",
  "data": {
    "user": {
      "id": 1,
      "name": "Иван Иванов",
      "email": "ivan@example.com"
    },
    "token": "1|abc123def456..."
  }
}
```

### Авторизация
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ivan@example.com",
    "password": "password123"
  }'
```

### Получение информации о пользователе
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 1|abc123def456..."
```

## Справочники

### Получение списка брендов
```bash
curl -X GET http://localhost:8000/api/car-brands
```

**Ответ:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Toyota",
      "created_at": "2025-08-26T10:00:00.000000Z",
      "updated_at": "2025-08-26T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "BMW",
      "created_at": "2025-08-26T10:00:00.000000Z",
      "updated_at": "2025-08-26T10:00:00.000000Z"
    }
  ]
}
```

### Получение моделей автомобилей
```bash
# Все модели
curl -X GET http://localhost:8000/api/car-models

# Модели конкретного бренда
curl -X GET "http://localhost:8000/api/car-models?car_brand_id=1"
```

## Управление автомобилями

### Создание автомобиля
```bash
curl -X POST http://localhost:8000/api/cars \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Content-Type: application/json" \
  -d '{
    "car_brand_id": 1,
    "car_model_id": 1,
    "year": "2020",
    "color": "Красный",
    "mileage": 50000
  }'
```

**Ответ:**
```json
{
  "success": true,
  "message": "Автомобиль успешно добавлен",
  "data": {
    "id": 1,
    "car_brand_id": 1,
    "car_model_id": 1,
    "year": "2020",
    "color": "Красный",
    "mileage": 50000,
    "created_at": "2025-08-26T10:00:00.000000Z",
    "updated_at": "2025-08-26T10:00:00.000000Z",
    "car_brand": {
      "id": 1,
      "name": "Toyota"
    },
    "car_model": {
      "id": 1,
      "name": "Camry"
    }
  }
}
```

### Получение списка автомобилей пользователя
```bash
curl -X GET http://localhost:8000/api/cars \
  -H "Authorization: Bearer 1|abc123def456..."
```

### Получение информации об автомобиле
```bash
curl -X GET http://localhost:8000/api/cars/1 \
  -H "Authorization: Bearer 1|abc123def456..."
```

### Обновление автомобиля
```bash
curl -X PUT http://localhost:8000/api/cars/1 \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Content-Type: application/json" \
  -d '{
    "color": "Синий",
    "mileage": 60000
  }'
```

### Удаление автомобиля
```bash
curl -X DELETE http://localhost:8000/api/cars/1 \
  -H "Authorization: Bearer 1|abc123def456..."
```

## Совместное использование автомобилей

### Прикрепление существующего автомобиля
```bash
curl -X POST http://localhost:8000/api/cars/1/attach \
  -H "Authorization: Bearer 1|abc123def456..."
```

### Открепление автомобиля
```bash
curl -X DELETE http://localhost:8000/api/cars/1/detach \
  -H "Authorization: Bearer 1|abc123def456..."
```

### Предоставление доступа другому пользователю
```bash
curl -X POST http://localhost:8000/api/cars/1/share \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Content-Type: application/json" \
  -d '{
    "user_email": "friend@example.com"
  }'
```

**Ответ:**
```json
{
  "success": true,
  "message": "Автомобиль успешно предоставлен в пользование",
  "data": {
    "shared_with": {
      "id": 2,
      "name": "Анна Петрова",
      "email": "friend@example.com"
    }
  }
}
```

### Получение списка пользователей автомобиля
```bash
curl -X GET http://localhost:8000/api/cars/1/users \
  -H "Authorization: Bearer 1|abc123def456..."
```

**Ответ:**
```json
{
  "success": true,
  "data": {
    "car": {
      "id": 1,
      "car_brand": {
        "name": "Toyota"
      },
      "car_model": {
        "name": "Camry"
      }
    },
    "users": [
      {
        "id": 1,
        "name": "Иван Иванов",
        "email": "ivan@example.com"
      },
      {
        "id": 2,
        "name": "Анна Петрова",
        "email": "friend@example.com"
      }
    ]
  }
}
```

## Обработка ошибок

### Ошибка валидации (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field must be at least 8 characters."
    ]
  }
}
```

### Ошибка аутентификации (401)
```json
{
  "message": "Unauthenticated."
}
```

### Ошибка доступа (403)
```json
{
  "success": false,
  "message": "Доступ запрещен. Вы можете управлять только своими автомобилями."
}
```

### Ресурс не найден (404)
```json
{
  "success": false,
  "message": "Автомобиль не найден"
}
```

### Конфликт (409)
```json
{
  "success": false,
  "message": "Автомобиль уже добавлен к этому пользователю"
}
```

## Статус коды

- **200** - Успешный запрос
- **201** - Ресурс создан
- **400** - Неверный запрос
- **401** - Не авторизован
- **403** - Доступ запрещен
- **404** - Ресурс не найден
- **409** - Конфликт
- **422** - Ошибка валидации
- **500** - Внутренняя ошибка сервера

## Постман коллекция

Для удобства тестирования можно импортировать коллекцию Postman:

```bash
# Экспорт OpenAPI документации в Postman
curl -X GET http://localhost:8000/api/documentation.json > car-management-api.json
```

Затем импортировать файл в Postman: `File > Import > car-management-api.json`

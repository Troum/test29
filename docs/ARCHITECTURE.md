# Архитектура Car Management API

## Общие принципы

Проект построен на основе **Clean Architecture** с использованием современных паттернов проектирования:

- **Repository Pattern** - абстракция работы с данными
- **Service Layer** - бизнес-логика приложения
- **DTO Pattern** - типизированная передача данных
- **Dependency Injection** - слабая связанность компонентов

## Структура проекта

```
app/
├── Http/
│   ├── Controllers/         # Контроллеры (HTTP слой)
│   ├── Requests/           # Валидация запросов
│   └── Middleware/         # Промежуточное ПО
├── Services/               # Бизнес-логика
├── Repositories/           # Работа с данными
├── Models/                 # Eloquent модели
├── DTOs/                   # Объекты передачи данных
├── Contracts/              # Интерфейсы
├── Providers/              # Сервис-провайдеры
└── OpenApi/               # OpenAPI схемы
```

## Слои архитектуры

### 1. HTTP слой (Controllers)

**Ответственность:** Обработка HTTP запросов и формирование ответов

```php
// app/Http/Controllers/CarController.php
class CarController extends Controller
{
    public function __construct(
        private readonly BaseServiceInterface $carService,
        private readonly CarOwnershipService $ownershipService,
        private readonly UserService $userService
    ) {}
    
    public function store(CarCreateRequest $request): JsonResponse
    {
        $dto = CarCreateDTO::fromRequest($request);
        $car = $this->carService->createOne($dto->toArray());
        // ...
    }
}
```

**Принципы:**
- Тонкие контроллеры - только маршрутизация и формирование ответов
- Валидация через Form Requests
- Использование DTO для передачи данных

### 2. Бизнес-логика (Services)

**Ответственность:** Реализация бизнес-правил и координация операций

```php
// app/Services/CarService.php
class CarService implements BaseServiceInterface
{
    public function __construct(
        private BaseRepositoryInterface $carRepository
    ) {}
    
    public function createOne(array $data): mixed
    {
        return $this->carRepository->createOne($data);
    }
}
```

**Принципы:**
- Инкапсуляция бизнес-логики
- Зависимость от интерфейсов, а не от конкретных классов
- Координация между различными компонентами

### 3. Слой данных (Repositories)

**Ответственность:** Абстракция работы с базой данных

```php
// app/Repositories/CarRepository.php
class CarRepository implements BaseRepositoryInterface
{
    public function createOne(array $data): ?Car
    {
        return DB::transaction(function () use ($data) {
            return Car::create($data);
        });
    }
}
```

**Принципы:**
- Единственная точка доступа к данным
- Инкапсуляция SQL запросов
- Транзакционность операций

### 4. Модели данных (Models)

**Ответственность:** Представление структуры данных и связей

```php
// app/Models/Car.php
class Car extends Model
{
    protected $fillable = [
        'car_brand_id', 'car_model_id', 'year', 'color', 'mileage'
    ];
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_car')
            ->withTimestamps();
    }
}
```

### 5. Объекты передачи данных (DTOs)

**Ответственность:** Типизированная передача данных между слоями

```php
// app/DTOs/CarCreateDTO.php
readonly class CarCreateDTO extends BaseDTO
{
    public function __construct(
        public int $carBrandId,
        public int $carModelId,
        public ?string $year = null,
        public ?string $color = null,
        public ?int $mileage = null
    ) {}
    
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            carBrandId: $request->validated('car_brand_id'),
            carModelId: $request->validated('car_model_id'),
            year: $request->validated('year'),
            color: $request->validated('color'),
            mileage: $request->validated('mileage')
        );
    }
}
```

## Паттерны проектирования

### Repository Pattern

Абстрагирует доступ к данным от бизнес-логики:

```php
interface BaseRepositoryInterface
{
    public function getAll(array $relations = []): mixed;
    public function getOne(int $id, array $relations = []): mixed;
    public function createOne(array $data): mixed;
    public function updateOne(Model $model, array $data): mixed;
    public function deleteOne(Model $model, bool $force = false): mixed;
}
```

### Service Layer

Инкапсулирует бизнес-логику:

```php
interface BaseServiceInterface
{
    public function getAll(array $relations = []): mixed;
    public function getOne(int $id, array $relations = []): mixed;
    public function createOne(array $data): mixed;
    public function updateOne(Model $model, array $data): mixed;
    public function deleteOne(Model $model, bool $force = false): mixed;
}
```

### Dependency Injection

Конфигурация зависимостей в сервис-провайдерах:

```php
// app/Providers/RepositoryProvider.php
$this->app->when(CarService::class)
    ->needs(BaseRepositoryInterface::class)
    ->give(CarRepository::class);
```

## Принципы SOLID

### Single Responsibility Principle (SRP)
- Каждый класс имеет одну ответственность
- Контроллеры только обрабатывают HTTP
- Сервисы только реализуют бизнес-логику
- Репозитории только работают с данными

### Open/Closed Principle (OCP)
- Интерфейсы позволяют расширять функциональность
- Новые реализации без изменения существующего кода

### Liskov Substitution Principle (LSP)
- Любая реализация интерфейса взаимозаменяема
- CarRepository можно заменить на RedisCarRepository

### Interface Segregation Principle (ISP)
- Интерфейсы разделены по функциональности
- BaseRepositoryInterface, BaseServiceInterface

### Dependency Inversion Principle (DIP)
- Зависимость от абстракций, а не от конкретных классов
- Сервисы зависят от интерфейсов репозиториев

## Безопасность

### Аутентификация
```php
// Laravel Sanctum + Middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cars', [CarController::class, 'index']);
});
```

### Авторизация
```php
// Проверка прав доступа в сервисах
if (!$this->ownershipService->hasAccess($car)) {
    return response()->json([
        'success' => false,
        'message' => 'Доступ запрещен'
    ], 403);
}
```

### Валидация
```php
// Form Requests
class CarCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'car_brand_id' => ['required', 'integer', 'exists:car_brands,id'],
            'car_model_id' => ['required', 'integer', 'exists:car_models,id'],
        ];
    }
}
```

## Тестирование

### Структура тестов
```
tests/
├── Feature/           # Интеграционные тесты
│   ├── Auth/         # Тесты аутентификации
│   └── Cars/         # Тесты управления автомобилями
└── Unit/             # Модульные тесты
    ├── DTOs/         # Тесты DTO
    └── Services/     # Тесты сервисов
```

### Принципы тестирования
- **Feature тесты** - тестируют полный цикл HTTP запрос-ответ
- **Unit тесты** - тестируют отдельные компоненты
- **Моки** для внешних зависимостей
- **Фабрики** для создания тестовых данных

## Производительность

### Оптимизации
- **Eager Loading** для предотвращения N+1 запросов
- **Транзакции** для целостности данных
- **Кеширование** справочных данных
- **Индексы** в базе данных

### Мониторинг
- Логирование SQL запросов в режиме отладки
- Метрики производительности через Laravel Telescope
- Профилирование медленных запросов

## Расширяемость

### Добавление новых сущностей
1. Создать модель
2. Создать репозиторий и интерфейс
3. Создать сервис
4. Создать контроллер и requests
5. Настроить маршруты и DI

### Добавление новых функций
1. Расширить интерфейсы
2. Реализовать в сервисах
3. Добавить эндпоинты в контроллеры
4. Написать тесты

Архитектура позволяет легко добавлять новую функциональность без изменения существующего кода, следуя принципу Open/Closed.

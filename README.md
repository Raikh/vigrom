## About Vigrom

Simple Wallet App

# Deploy

### Сборка и запуск контейнеров
Для инициализации контейнера с БД (пользователь/пароль/база)  
В `docker-compose.yml` для контейнера `mysql` указать необходимые данные (MYSQL_DATABASE и т.д.)  
Для контейнреа `nginx` прописать `NGINX_WEB_DOMAIN`

Выполнить `make compose-env` (либо скопировать вручную `.env.example` -> `.env`)  
Выставить нужные энвы в `.env`  
* Доступ к БД
* APP_DEFAULT_CURRENCY - валюта системы "по-умолчанию" (По-умолчанию и для тестовых данных выставлено USD)

Для сборки и запуска контейнеров выполнить `make compose-up`

### Установка зависимостей и миграции
Команда `make compose-init` установит зависимости композера, запустит миграции

### Заполнение БД тестовыми данными
Команда `make compose-seed` заполнит БД тестовыми данными

### Обновление курсов валют
Команда `make compose-refresh-rates` сгенерирует новые курсы по валютам

### Тесты
make compose-test


Примеры API:

### Метод для получения текущего баланса 
Request: `GET` /api/wallet/{walletId}  
Response (Status 200)
```
{
    "wallet_currency": "RUB",
    "wallet_balance": "462497.19",
    "default_currency_code": "USD",
    "default_currency_balance": "102777.15",
    "rate": "4.500000000"
}
```
Response (Status 500)
```json
{
    "code": "internal_server_error",
    "title": "response_errors.internal_server_error",
    "status": 500,
    "details": {
        "message": [
            "not_found"
        ]
    }
}
```
### Метод для изменения баланса
Request: `POST` /api/wallet/transaction  
Request body example:
```json
{
    "wallet_id": 123,
    "iso_currency_code": "USD",
    "type": "debit",
    "reason": "refund",
    "amount": 1000 //Сумма в минимульных единицах для валюты (например копейки или центы)
}
```
Response (Status 200)
```json
{}
```
Response (Status 500)
```json
{
    "code": "internal_server_error",
    "title": "response_errors.internal_server_error",
    "status": 500,
    "details": {
        "message": [
            "not found"
        ]
    }
}
```

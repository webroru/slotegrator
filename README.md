### Docker ###
1. Ставим Docker: https://docs.docker.com/engine/installation/linux/ubuntu/
2. Ставим Docker Compose: https://docs.docker.com/compose/install/
3. Добавляем в /etc/hosts `172.16.237.1 app.local`

### API ###

**Создание нового пользователя**
----

* **URL:** `/register`

* **Метод:** `POST`

* **Параметры:**

    Данные в формате JSON

    `email=[string]`

    `password=[string]`

    `password_confirmation=[string]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    ```
    user=[
        id=[integer]
        email=[string]
        roles=[array]
        apiToken=[string]
    ]
    ```

* **Пример:**

    `curl -H "Content-Type: application/json" -X POST -d '{"email":"test@mail.com", "password":"123", "password_confirmation":"123"}' http://app.local:8080/register`


**Авторизация пользователя**
----

* **URL:** `/login`

* **Метод:** `POST`

* **Параметры:**

    Данные в формате JSON

    `email=[string]`

    `password=[string]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    ```
    user=[
        id=[integer]
        email=[string]
        roles=[array]
        apiToken=[string]
    ]
    ```

* **Пример:**

    `curl -H "Content-Type: application/json" -X POST -d '{"email":"test@mail.com", "password":"123"}' http://app.local:8080/login`


**Получение случайного приза**
----

* **URL:** `/play`

* **Метод:** `GET`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    ```
    winning=[
        id=[integer]
        award=[
            id=[integer]
            name=[string]
            type=[string]
        ]
        amount=[integer]
        isFinished=[boolean]
    ]
    ```

* **Пример:**

    `curl -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X GET http://app.local:8080/play`


**Перевод денег на счет в банке**
----

* **URL:** `/transferMoney`

* **Метод:** `POST`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Параметры:**

    Данные в формате JSON

    `winning_id=[integer]`

    `account_number=[string]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    `result=true`

* **Пример:**

    `curl -H "Content-Type: application/json" -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X POST -d '{"winning_id":1, "account_number":"000123"}' http://app.local:8080/transferMoney`



**Перевод Очков лояльности на аккаинт игрока**
----

* **URL:** `/transferLoyalty`

* **Метод:** `POST`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Параметры:**

    Данные в формате JSON

    `winning_id=[integer]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    `result=true`

* **Пример:**

    `curl -H "Content-Type: application/json" -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X POST -d '{"winning_id":1}' http://app.local:8080/transferLoyalty`



**Отправка физического приза**
----

* **URL:** `/transferPrize`

* **Метод:** `POST`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Параметры:**

    Данные в формате JSON

    `winning_id=[integer]`

    `address=[string]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    `result=true`

* **Пример:**

    `curl -H "Content-Type: application/json" -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X POST -d '{"winning_id":1, "address":"Any address, 123"}' http://app.local:8080/transferPrize`



**Конвериация денег в Очки лояльности**
----

* **URL:** `/convertMoney`

* **Метод:** `POST`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Параметры:**

    Данные в формате JSON

    `winning_id=[integer]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    `result=true`

* **Пример:**

    `curl -H "Content-Type: application/json" -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X POST -d '{"winning_id":1}' http://app.local:8080/convertMoney`


**Отказ от приза**
----

* **URL:** `/refusePrize`

* **Метод:** `POST`

* **Авторизация:**

    Заголовок `X-AUTH-TOKEN: [string]`

* **Параметры:**

    Данные в формате JSON

    `winning_id=[integer]`

* **Ответ в случае успешного запрса:**

    Данные в формате JSON

    `result=true`

* **Пример:**

    `curl -H "Content-Type: application/json" -H "X-AUTH-TOKEN: 3bf8fdee9b0790624e21e2b4117fe342" -X POST -d '{"winning_id":1}' http://app.local:8080/refusePrize`


### Обработка очереди ###

Для запуска обработки очереди неоплаченных выйгрышей необходимо запустить консольную команду:

`docker-compose exec php php bin/console messenger:consume --limit=N`, где N — количество обрабатываемых выйгрышей

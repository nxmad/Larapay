### Установка
1. Через Composer:
    ``` bash
    $ composer install skylex/larapay
    ```
2. Подключаем Service Provider в `config/app.php`:
    ``` php
    'providers' => [
        // ...
        
        Skylex\Larapay\LarapayServiceProvider::class,
    ],
    ```
3. Публикуем файл конфигураций  `larapay.php`
    ``` bash
    $ php artisan vendor:publish
    ```
4. Готово, можем приступать к работе

### Настройка
1. Для начала выполняем миграции (они добавят таблицу transactions)
    ``` bash
    $ php artisan migrate
    ```
2. Для доступа к транзакция понадобится модель `Transaction` наследующая `Skylex\Larapay\Models\Transaction`
3. Для значения `transaction` в конфиге `payments.php` пропишите
    обозначим путь до созданной модели, по умолчанию будет `\App\Transaction::class` 
4. Так же надо добавить трейт `Skylex\Larapay\Traits\Transactions` к планируемому инициатору
    транзакций. (Например, к модели `User`)
5. Осталось только настроить модули платежных систем,
    указания по настройке должны находится в репозитории модуля
    
### Пример использования
Пример использования с комментариями находится
    в [Examples/PaymentController.php](../blob/master/src/Examples/PaymentController.php)
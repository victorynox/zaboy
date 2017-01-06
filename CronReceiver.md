#CronReceiver

Для того что бы приложение могло обрабатывать очередь по хуку крона, вы должны внести в свой index.php
Слудующею запись 
```php

    $CronReceiverFactory = new CronReceiverFactory();
    $cron = $CronReceiverFactory($container, '');
    $app->pipe('/api/cron', $cron);

```

Где `/api/cron` - заменить на тут url по которому вы хотите получать запросы крона.

Так же вам нужно заменить хосты в файле конфига service.glob.php

```php
    'httpInterruptor' => [
        'url' => 'http://localhost:9090/api/http'
    ],
    'cronQueue' => [
        'url' => 'http://localhost:9090/api/cron'
    ],
```
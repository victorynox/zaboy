Для того что бы приложение могло обрабатывать очередь по хуку крона, вы должны внести в свой index.php
Слудующею запись 
```php
$CronReceiverFactory = new CronReceiverFactory();
$cron = $CronReceiverFactory($container, '');
$app->pipe('/api/cron', $cron);
```
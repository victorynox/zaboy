#UTILS
##PHP SERIALIZER

###Чего не хватает в PHP serialize.

####Загрузка сервисов из ServiceManager (например DB adapter)

####Результат сериализации не строка (могут быть 0 байты и спец символы)
Результат сериализации кодируется в Base64. Перед десериализацие - обратное преобразование.

[Coder.php](https://github.com/avz-cmf/zaboy/blob/master/src/utils/Json/Coder.php/).

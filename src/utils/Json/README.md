#UTILS
##JSON CODER AND SERIALIZER

Coder и Serializer имею похожий функционал, но отличаются способом сериализации объектов.

###JSON CODER
[Coder.php](https://github.com/avz-cmf/zaboy/blob/master/src/utils/Json/Coder.php/).
В основном используется для сериализации простых типов и массивов.

###JSON SERIALIZER
[Serializer.php](https://github.com/avz-cmf/zaboy/blob/master/src/utils/Json/Serializer.php/).
В основном используется для сериализации простых объектов.

##В чем отличия
JSON CODER может декодировать ассоциативные массивы с численными ключами. Это связано стем, что JSON представление объекта декодируется в массив.

JSON SERIALIZER ассоциативные массивы преобразует в JSON представление объекта и при обратном преобразовании рассматривает индексы как имена полей объекта.
При наличии цифровых индексов - выбрасывает исключение.

Подробнее видно в тестах:
[CoderTest.php](https://github.com/avz-cmf/zaboy/blob/master/src/utils/Json/Coder.php/),
[SerializerTest.php](https://github.com/avz-cmf/zaboy/blob/master/src/utils/Json/Coder.php/).
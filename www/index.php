<?php

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';



$a = array('<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9");

echo "Обычно: ", json_encode($a), "\n";
echo "Тэги: ", json_encode($a, JSON_HEX_TAG), "\n";
echo "Апострофы: ", json_encode($a, JSON_HEX_APOS), "\n";
echo "Кавычки: ", json_encode($a, JSON_HEX_QUOT), "\n";
echo "Амперсанды: ", json_encode($a, JSON_HEX_AMP), "\n";
echo "Юникод: ", json_encode($a, JSON_UNESCAPED_UNICODE), "\n";
echo "Все: ", json_encode($a, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), "\n\n";

$b = array();

echo "Отображение пустого массива как массива: ", json_encode($b), "\n";
echo "Отображение пустого массива как объекта: ", json_encode($b, JSON_FORCE_OBJECT), "\n\n";

$c = array(array(1, 2, 3));

echo "Отображение неассоциативного массива как массива: ", json_encode($c), "\n";
echo "Отображение неассоциативного массива как объекта: ", json_encode($c, JSON_FORCE_OBJECT), "\n\n";

$d = array('foo' => 'bar', 'baz' => 'long');

echo "Ассоциативный массив всегда отображается как объект: ", json_encode($d), "\n";
echo "Ассоциативный массив всегда отображается как объект: ", json_encode($d, JSON_FORCE_OBJECT), "\n\n";



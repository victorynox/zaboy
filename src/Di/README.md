# DI - InsideConstruct

##Быстрый старт

###Обычная практика

Пусть у нас есть класс, принимающий 3 сервиса в качестве зависимостей:

    class Class1
    {
        public $propA;
        public $propB;
        public $propC;

        public function __construct($propA = null, $propB = null, $propC = null)
        {
            $this->propA = $propA;
            $this->propB = $propB;
            $this->propC = $propC;
        }
    }

    /* @var $contaner ContainerInterface */
    global $contaner;
    $propA = $contaner->has('propA') ? $contaner->get('propA') : null;
    $propB = $contaner->has('propB') ? $contaner->get('propB') : null;
    $propC = $contaner->has('propC') ? $contaner->get('propC') : null;

    new Class1($propA, $propB, $propC);

Мы получили из контейнера зависимости и присвоили их одноименным свойствам объекта.

###Теперь то-же самое с использованием `InsideConstruct::initServices()`:

Если имя параметра соответствует имени сервиса и имени свойства объекта:


    class Class1
    {

        public $propA;
        public $propB;
        public $propC;

        public function __construct($propA = null, $propB = null, $propC = null)
        {
            InsideConstruct::initServices();
        }

    }

    new Class1();

Все три сервиса будут инициализированы сервисами из `$containr` как в примере выше.  
Вызов `InsideConstruct::initServices()` не изменяет переданные в констрактор параметры.  
Если у параметров констрактора указаны тип или интерфейс, то сервисы, полученные вызовом 
`InsideConstruct::initServices()` будут проверены на соответствие.  
Инициализируются `Public`, `Protected`, и `Private` свойства объекта. Не инициализируются `Static` свойства и `Private` свойства предков.
 
##Использование

### Как перекрыть умолчания
Если так:

            new Class1(new stdClass(), null);
то только один (последний) параметр будет инициализирован сервисом `$contaner->get('propC')`.  
Два других получат значения `new stdClass(`) и `null`. Но присваивания свойствам объекта или вызовы сеттеров (см. далее) отработают для всех параметров. 


### Сеттеры  (`$this->setPropA($value)`)
Если для параметра констрактора определен соответствующий (по имени) сеттер - он будет вызван. Сеттеры имеют приоритет над свойствами. Если для параметра есть и сеттер и свойство, то будет вызван сеттер, а присваивание свойству не будет произведено.





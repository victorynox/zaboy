# DI
##InsideConstruct =

###Бытрый старт


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

#### Если имя параметра соответствует имени сервиса и имени свойства объекта:

Теперь то-же самое использованием `InsideConstruct::initServices()`:

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

Все три сервиса будут инициализированы.  
Вызов `InsideConstruct::initServices()` не изменяет переданные в констрактор параметры.  
Если у параметров констрактора указаны тип или интерфейс, то сервисы, полученные вызовом 
`InsideConstruct::initServices()` будут проверены на соответствие.  
Инициализируются `Public`, `Protected`, и `Private` свойства объекта. Не инициализируются `Static` свойства и `Private` свойства предков.
 
#### Как перекрыть умолчания
Если так:


            new Class1(new stdClass(), null);
то только один (последний) параметр будет инициализирован вызовом `InsideConstruct::initServices()`.  
Два других получат значения `new stdClass(`) и `null`.





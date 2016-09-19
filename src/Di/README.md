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





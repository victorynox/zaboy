#Promise

##Быстрый старт
**Promise** - это просто контейнер для хранения результата операции.  
Вы можете:  

- его создать:` $promise = new Promise();`  
- заполнить: ` $promise = $promise->resolve('foo');`  
- считать результат:` $resalt = $promise->wait();//'foo'`

Еще можно сказать, что сделать с результатом, когда им будет заполнен контейнер. Метод
     
	public function then(callable $onFulfilled = null, callable $onRejected =null);
позволяет задать колбеки для обработки результата или исключения. Пример:

	use zaboy\async\Promise\Promise;

	$masterPromise = new Promise;
	$slavePromise = $masterPromise->then(function($val) {
	    var_dump('Hello ' . $val);
	});
	$masterPromise->resolve('World');	//string 'Hello World' (length=11)


##Статусы
###Статусы согласно стандарту Promise A+
Согласно стандарту **Promise** может находится в одном из трех статусов:  
  
- 'pending';  
- 'fulfilled';  
- 'rejected';

В большинстве случаев для работы с **Promise** этого достаточно, но давайте рассмотрим, что проиходит, когда вы "кладете" в **Promise** в качестве результата
другой **Promise**. По стандарту, первый зависимый (slave) **Promise** принимает статус переданного (master) **Promise**.  
Если  **masterPromise** определен ('fulfilled' или 'rejected'), то **slavePromise** будет заполнен значением из **masterPromise** и перейдет в соответствующий статус.
Но если **masterPromise** в статусе 'pending', то **slavePromise** будет ждать, пока **masterPromise** "определится". 

###Четвертый статус - 'dependent'
Так в каком статусе находится **slavePromise**? Формально - 'pending', но при этом, он заблокирован для изменений извне.
Повлиять на его статус может только изменение статуса **masterPromise**. Попытки вызвать у **slavePromise** метод 
reject($reason) или resolve($value) вызовут исключение.   
Таким образом мы имеем четвертое состояние **Promise** - 'dependent', который является частным случаем статуса 'pending'.


##Интерфейс
### Методод getState()
Метод `getState(true)` или просто `getState()` возвращает один из трех статусов.
Вызов `getState(false)` может вернуть дополнительный статус 'dependent', который по умолчанию 
представлен статусом 'pending'.  

    public function getState($dependentAsPending = true);
Вызов метода `getState()` может вернуть одно из значений:

- 'pending';  
- 'fulfilled';  
- 'rejected';  

Вызов метода `getState(false)` может вернуть еще одно значение:

- 'dependent';


### Метод reject($reason)
Вызов метода `reject($reason)` говорит о том, что попытка получить результат закончилась неудачей. Параметр `$reason` это либо Exception, либо сообщение (`string`), с которым будет сгенерирован `Exception`.  

	public function reject($reason);
Переводит **Promise** из статуса 'pending' в 'rejected'.  
При вызове у **Promise** в статусе 'fulfilled' - бросает исключение `AlreadyFulfilledException`
При вызове у **Promise** в статусе 'rejected' - бросает исключение `AlreadyRejectedException`, если 
аргумент $reason не эквивалентен ранее переданному значению.   
При вызове у **Promise** в статусе 'dependent' - бросает исключение `AlreadyResolvedException`, если 
аргумент $reason не является исключением типа `TimeIsOutException`.   




----------

----------


	public function reject($reason);
	public function resolve($value);

	public function wait($unwrap = true);
    public function then(callable $onFulfilled = null, callable $onRejected = null);


 

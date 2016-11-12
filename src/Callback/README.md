#Callback , Interruptor и Promiser   


----------

## Callback
 
Это invockable объект -  обертка для **Callable**.   
Умеет сериализовываться, даже если в него "обернуто" замыкание (анонимная функция).

###Как работает Callback:
	$callable = function($val){return 'Hello ' . $val;};
	$callback = new Callback($callable); // $callable - is any type of \Callable
	var_dump($callback('World')); //'Hello World'

###Если результат нужен в виде Promise:
	...
    $promise = new Promise();
	$resultPromise = $promise->then($callback);
	$promise->resolve('World'); //run!
	var_dump($resultPromise->wait()); //'Hello World'


## Interruptor

Разновидность **Callback** для "параллельного" выполнения кода (на другом сайте, в другом процессе, ч-з очередь ...). Обычно вызов `$interruptor()` не возвращает результат выполнения **Callable**, зато сразу возвращает управление.   
Например в `Interruptor\Process` стартует новый процесс.  После вызова  `$info = $interruptor()`, в `$info` будет массив с информацией о процессе (PID, ...).  
Если нужен результат выполнения **Callable**, используйте **Promise**.




## Promiser

###Если результат нужен в виде Promise:
	$callable = function($val){return 'Hello ' . $val;};
    $promiser = new Promiser($callable);
	$resultPromise = $promiser('World');
	var_dump($resultPromise->wait()); //'Hello World'

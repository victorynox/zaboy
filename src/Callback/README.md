# Callback
##Что это и зачем
Это invockable объект -  обертка для Callable.  
Умеет сериализовываться и возвращать (обрабатывать) Promises

##Бытрый старт

###Как работает Callback:
	$callable = function($val){return 'Hello ' . $val};
	$callback = new Callback($callable);
	var_dump($callback('World')); //'Hello World'

###Если результат нужен в виде Promise:
	$callable = function($val){return 'Hello ' . $val};
	$callback = new Callback($callable);
	$promise = $callback('World', true)); //true!
	var_dump($promise->wait()); //'Hello World'

###Что такое $interruptor
Это объект, который умеет запускать Callback параллельно (на другом сайте, в другом процессе, ч-з очередь ...).  
Принимает сериализуемый Callable, параметр для передачи в Callable, и Promise для результата.  
Возвращает Promise.

###На что влияют параметры $interruptor и $promise
	$callback = new Callback($callable,  $interruptor = null);
	$result =  $callback($val, $promise = null));

<table>
	<thead>
		<tr>
		  <th>,</th>
		  <th>$interruptor = null</th>
		  <th>$interruptor = new \Interrupter()</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<b>$promise = null
			</td>
			<td>
				Создание колбэка <br> <code>new Callback($propCallable);</code><br> 
				Вызов колбэка: <br> <code>$callback($val));</code><br>
				Что делает: <br> просто исполняет <code>$propCallable(val)</code>
			</td>
			<td>
				Создание колбэка <br> 
				<code>$interruptor = new Interrupter();<br> 
				new Callback($propCallable, $interruptor);</code><br> 
				Вызов колбэка: <br> <code>$callback($val));</code><br>
				Что делает: <br> Бросает исключение (Нет второго параметра)
			</td>
		</tr>
				<tr>
			<td>
				<b>$promise = new \Promise()
			</td>
			<td>
				Создание колбэка<br>  <code>new Callback($propCallable);
				</code><br> 
				Вызов колбэка: <br> <code>$callback($val, $promise)); //$promise</code> <br>
				Что делает:  <br> Оборачивает результат в переданный Promise. <br>
				<code>
				$result =$propCallable(val); <br>
				return  promise->resolve($result);<br></code>
				или <br>
				<code>
				return  $promise->reject(new \Exception());<br></code>
				</code>
			</td>
			<td>
				Создание колбэка <br> 
				<code>$interruptor = new Interrupter();<br> 
				new Callback($propCallable, $interruptor);</code><br> 
				Вызов колбэка:<br>  <code>$callback($val,  $promise)); //$promise</code> <br>
				Что делает: <br> 
				Вызывает интераптор 
				<code>$result = $interruptor($val, $propCallable, $promise );</code> <br>
				и возвращает $result - это должен быть промайс, который передан интераптору.
			</td>
		</tr>
	</tbody>
</table>
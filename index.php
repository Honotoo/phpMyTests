<?php


?> 
	
<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8">
  <title>Решения</title>
 </head>
 <body>
	<div>
	<label>Задание на обработку массивов. Решено с помощью объектов</label>
	<form action="task1.php">
	<input type="submit" value="задание 1">
	</form>
	<br>
	</div>
		
	<div>
	<label>Задание 2 на удаление из бд строк не имеющим значением в связанных таблицах. </label>
	<form action="task2.php">
	<input type="submit" value="задание 2">
	</form>
	<br>
	</div>
	
	<div>
	<label>задание на защиту от инъекций. задача решается разделением с помощью PDO операции на подготовку команды SQL и отдельно на внесение данных пользователя. Инъекция вызовет ошибку</label>
	<form action="task3.php">
	<input type="submit" value="задание 3">
	</form>
	<br>
	</div>
	
	<div>
	<label>Готовая система комментариев. 
На клиентской стороне все действия обрабатываются функциями в JS. На серверной стороне комментарии страницы объекты. 
Их можно сериализовать или сохранить в формате дерева и использовать ORM </label>
	<form action="comments/comments.php">
	<input type="submit" value="задание 3+">
	</form>
	<br>
	</div>
	
	<div>
	<label>Обрезка строки по указанному количеству слов</label>
	<form action="task4.php">
	<input type="submit" value="задание 4">
	</form>
	<br>
	</div>
	
 </body>
</html>
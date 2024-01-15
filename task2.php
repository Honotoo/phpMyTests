<?php
// Подготовка данных

function createDB()
{
	$localhost = "localhost";
	$username = "root";
	$password = "";
	$dbname = "TestDTB";

	$connect = new mysqli($localhost, $username, $password);
	if($connect->connect_error) {
		die("connection failed : " . $connect->connect_error);} 
		else {echo "database connected";}
	echo "<br>";
	$sqlQuery = "CREATE DATABASE ".$dbname."";
	if($connect->query($sqlQuery) === TRUE) {
		echo "database created";
	} else {
		echo "database NOT created";
		echo 'Error '.$connect->error."<br><br>";
	}
	$connect->close();
}




function makeQuery($sqlQuery)
{
	$localhost = "localhost";
	$username = "root";
	$password = "";
	$dbname = "TestDTB";

	$connect = new mysqli($localhost, $username, $password,$dbname );
	if($connect->connect_error) {
		die("connection failed : " . $connect->connect_error);} 
		
	$result = $connect->query($sqlQuery);
	
	if($result === TRUE or gettype($result) == "object") {
		//echo "query done <br>";
	} 
	else {
		echo 'Error: '.$connect->error.' <br><br>';
	} 
	$connect->close();
	return $result;
}

function makeDefault()
{
	$queryes =[ '
	CREATE TABLE IF NOT EXISTS availabilities (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  amount int(11) NOT NULL,
	  product_id int(11) NOT NULL,
	  stock_id int(11) NOT NULL,
	  PRIMARY KEY (id)
	) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	',
	'
	INSERT INTO availabilities (id, amount, product_id, stock_id) VALUES
	(1, 3, 1, 1),
	(2, 2, 1, 5),
	(5, 5, 2, 1),
	(6, 2, 5, 5),
	(9, 1, 6, 1),
	(10, 1, 6, 5);
	',
	'
	CREATE TABLE IF NOT EXISTS categories (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  title varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (id)
	) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	',
	'
	INSERT INTO categories (id, title) VALUES
	(1, "Электроника"),
	(2, "Бытовая техника"),
	(5, "Аксессуары"),
	(6, "Расходные материалы"),
	(9, "Мебель"),
	(10, "Товары для дачи");
	',
	'
	CREATE TABLE IF NOT EXISTS products (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  title varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	  category_id int(11) NOT NULL,
	  PRIMARY KEY (id)
	) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	',
	'
	INSERT INTO products (id, title, category_id) VALUES
	(1, "Телевизор LG", 1),
	(2, "Смартфон Samsung", 1),
	(5, "Микроволновая печь Redmond", 2),
	(6, "Кухонная вытяжка Elica", 2),
	(9, "Кабель питания HDMI", 6),
	(10, "Сетевой фильтр", 6);
	',
	'
	CREATE TABLE IF NOT EXISTS stocks (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  title varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (id)
	) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	',
	'
	INSERT INTO stocks (id, title) VALUES
	(1, "Главный склад"),
	(2, "Склад на Невской"),
	(5, "Склад на Бакинской");
	'];

	foreach ($queryes as $query)
	{
		 makeQuery($query);
	}
}
function dropAll()
{
	try{
		$queryes =[
		'DROP TABLE IF EXISTS availabilities;',
		'DROP TABLE IF EXISTS categories;',
		'DROP TABLE IF EXISTS products;',
		'DROP TABLE IF EXISTS stocks;' ];
		foreach ($queryes as $query)
		{
			 makeQuery($query);
		}
	} 
	catch (Exception $e) {
		createDB();
		makeDefault();
	}
}
function resetDb()
{
	dropAll();
	echo "deleted all <br>";
	makeDefault();
	echo "created all <br>";
}

function start(){
			echo '<!DOCTYPE HTML>
			<html>
			<head>
			<meta charset="utf-8">
			<title>Таблица размеров обуви</title>
			</head>
			<body>';
}

function printTableBody($TableReturned, $head = False) // принтует всю таблицу как таблицу 
{	
	echo '<table border="1">';
	if ($head != False){
		while($row = $head->fetch_assoc()) 
		{	
			foreach($row as $cell) { echo '<th> '.$cell.' </th>'; }
		}
	}
	
	while($row = $TableReturned->fetch_assoc()) 
	{	echo "\t<tr>\n";
		foreach($row as $cell)
		{ echo '<td> '.$cell.' </td>'; }
		echo "\t</tr>\n";
	}
	echo "</table>\n";
}

function printTable($tableName)
{	
	$sqlQuery = "SELECT COLUMN_NAME 
	FROM INFORMATION_SCHEMA.COLUMNS 
	WHERE TABLE_SCHEMA = Database() 
	AND TABLE_NAME = '".$tableName."' 
	ORDER BY ordinal_position ;";
	$head =  makeQuery($sqlQuery);
	
	$sqlQuery = 'SELECT * FROM '.$tableName.';';
	$body =  makeQuery($sqlQuery);
	echo "<br>".$tableName;
	printTableBody($body, $head);
}



resetDb();

start();
echo "<br><br><br>Таблицы до<br>";
printTable('availabilities');
printTable('categories');
printTable('products');
printTable('stocks');




echo "<br>------------------------------------------------------<br>";
echo "Таблицы после<br>";




echo "<br>есть пустые группы (без товаров)<br>";
$sqlQuery = 'DELETE categories FROM categories 
WHERE categories.id IN (
	SELECT * FROM(
		SELECT DISTINCT categories.id FROM categories 
		LEFT JOIN products ON categories.id=products.category_id WHERE products.category_id IS NULL
	) AS cat
);';
makeQuery($sqlQuery);  
printTable('categories');






echo "<br>есть товары без наличия<br>";
$sqlQuery = '
DELETE products FROM products 
WHERE products.id IN (
	SELECT * FROM(
		SELECT DISTINCT products.id  FROM products 
		LEFT JOIN availabilities ON products.id=availabilities.product_id WHERE availabilities.product_id IS NULL
	) AS pr
);';
makeQuery($sqlQuery);  
printTable('products');




echo "<br>есть склады без товаров<br>";
$sqlQuery = '
DELETE stocks FROM stocks 
WHERE stocks.id IN (
	SELECT * FROM(
	SELECT DISTINCT stocks.id FROM stocks 
	LEFT JOIN availabilities ON stocks.id=availabilities.stock_id WHERE availabilities.stock_id IS NULL
	) AS pr
);';
makeQuery($sqlQuery);  
printTable('stocks');



echo '<br><br>'.date('Y/m/d - H:i:s');

?>

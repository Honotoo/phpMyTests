<?php
// подготовка
function createDB()
{
	$localhost = "localhost";
	$username = "root";
	$password = "";
	$dbname = "TestDTB";

	$connect = new mysqli($localhost, $username, $password);
	if($connect->connect_error) {
		die("connection failed : " . $connect->connect_error);} 
	$sqlQuery = "CREATE DATABASE ".$dbname."";
	if($connect->query($sqlQuery) === TRUE) {
		//echo "database created";
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
	$queryes =[ 'CREATE TABLE IF NOT EXISTS comments (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  comment TEXT(128) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (id)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	',
	'
	INSERT INTO comments (id, comment) VALUES
	(1, "Комментарий 1"),
	(2, "Ещё комментарий");
	'];

	foreach ($queryes as $query)
	{
		 makeQuery($query);
	}
}

function dropAll()
{
	$queryes =[
	'DROP TABLE IF EXISTS comments;',
	];
	foreach ($queryes as $query)
	{
		 makeQuery($query);
	}
}
function resetDb()
{		
	createDB();
	dropAll();
	makeDefault();
}





echo "функционал защиты от инъекций путем разделение с помощью PDO операции на подготовку команды SQL и Внесение данных пользователя. Инъекция вызовет ошибку.<br><br><br>";



function printComments()
{
	try{
	$pdo = new PDO('mysql:dbname=TestDTB;host=localhost;charset=utf8mb4', 'root', '');
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $pdo->prepare('SELECT comment FROM comments');
	$stmt->execute([]);

	foreach ($stmt as $row) {
		echo '<div class="wrapper" style="margin-left: 0px;"><div style="background-color: rgb(255, 238, 168)">'.$row['comment'].'</div></div> ';
	} 
	}catch (Exception $e) {
		resetDb();
		printComments();
	}
	
}

function addNewComment($commentText)
{
	try{
		$pdo = new PDO('mysql:dbname=TestDTB;host=localhost;charset=utf8mb4', 'root', '');
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $pdo->prepare('INSERT INTO comments (comment) VALUES (:comment);');
		$stmt->execute([ 'comment' => $commentText ]);
	}catch (Exception $e) {
		resetDb();
		printComments();
	}
}

?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="commentStyle.css">
</head>
<body>
<div class="container"> 
	<form action='task3.php' method='post' >
	<input type="text" name="commentText" style="background-color: rgb(255, 238, 168); padding: 16px 32px; margin-left: 0px;" value=" текст комментария "  /><br>
	<input type="submit"  name="addComment" style="color: white; background-color: darkblue; padding: 6px 32px; margin-left: 0px;"  value="добавить комментарий" />
	</form>
    <div id="allComments">
	<?php
	if(!isset($_POST['addComment']))
	{ printComments(); }
	?> 
	</div>
</div>
</body>
</html>
<?php
if(isset($_POST['addComment']))
{ 
	addNewComment($_POST['commentText']);
	printComments();
}
?> 
<?php


echo "решение с максимальной возможностью поддержки изменений. Инкапсуляция соблюдается. HTML в отдельном классе View<br><br><br>";


class View
{
	public function startTable()
	{
		echo '<!DOCTYPE HTML>
			<html>
			<head>
			<meta charset="utf-8">
			<title>Таблица размеров обуви</title>
			</head>
			<body>
			<table border="1">
			<th>  </th><th> математика </th><th> ОБЖ </th><th> Физика </th>';
	}
	public function printUser($userObject)
	{			
		echo "\t<tr>\n";
			echo '<td> '.$userObject->Name." </td><td> ".$userObject->math.' </td><td> '.$userObject->obj.' </td><td> '.$userObject->physics.' </td>';
		echo "\t</tr>\n";
	}
	public function endTable()
	{
		echo "</table>\n";
	}
}

class User
{
	public $Name = "";
	public $math = 0;
	public $obj = 0;
	public $physics = 0;
	
	public function __construct(string $name){
		$this->Name = $name;
	}
	
	public function addValue($subject, $value)
	{
		switch ($subject) {
		case 'Математика':
			$this->math +=$value;
			break;
		case 'Физика':
			$this->physics +=$value;
			break;
		case 'ОБЖ':
			$this->obj +=$value;
			break;
		}
	}
	
	public function getPrinted($viewObject)
	{
		$viewObject->printUser($this);
	}
}


class tableArray {
	public $users = array();
	

	function sortArrBy($sortingArray, $sortColumn)
	{
		$result = [];
		foreach ($sortingArray as $row) 
		{
			$result[$row->{$sortColumn}] = $row;
		}
		ksort($result);
		return $result;
	}
	
	public function getUserByName($Name)
	{
		$foundUser = Null;
		foreach($this->users as $user)
		{ 
			if($user->Name == $Name)	
			{
				return $user; 
				break;
			}
		}
		return $foundUser;
	}
	
	public function printAllUsers($viewObject) 
	{
		$sortedUsers = $this->sortArrBy($this->users , 'math');
		foreach ($sortedUsers as $user) {
			$user->getPrinted($viewObject);
		}
	}
	
	public function parseThisArr($lineArr)
	{
		$user = $this->getUserByName($lineArr[0]);
		if ($user == Null)
		{
			$user = new User($lineArr[0]);
			array_push($this->users, $user);
		}
		$user->addValue($lineArr[1], $lineArr[2]);
	}
}



function printByArray($array)
{
	$tableObject = new tableArray();
	$viewObject = new View();
	
	foreach($array as $lineArr)
	{
		$tableObject -> parseThisArr($lineArr);
	}
	$viewObject->startTable();
	$tableObject->printAllUsers($viewObject);
	$viewObject->endTable();
}


$data = [
	['Иванов', 'Математика', 5],
	['Иванов', 'Математика', 4],
	['Иванов', 'Математика', 5],
	['Петров', 'Математика', 5],
	['Сидоров', 'Физика', 4],
	['Иванов', 'Физика', 4],
	['Петров', 'ОБЖ', 4],
];
printByArray($data);
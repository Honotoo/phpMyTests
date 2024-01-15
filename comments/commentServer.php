<?php


Class Comment
{	
	private $I = 0;//ID
	private $T = '';//Text
	private $D = '';//Date
	private $A = '';//Author
	private $L = 0;//Likes
	private $H = 0;//Hates
	private $R = array();// Reply array
	
	public function __construct(int $commentID,string $commentTEXT,string $dateTime,string $authorLogin) {
        $this->I = $commentID;
        $this->T = $commentTEXT;
        $this->D = $dateTime;
        $this->A = $authorLogin;
        $this->L = 0;
        $this->H = 0;
    }
	
	public function giveID(){return $this->I;}
	
	public function getPrinted($bias = 0, $specificID = 0) // $specificID = 0 значит все комменты
	{
		if ($specificID == 0 or $specificID == $this->I ){
			$toPrint = '<div class="wrapper" id="'.$this->I.'" style="margin-left: '. 15*$bias.'px;"><div style="background-color: rgb(255, 238, 168);">'.$this->A.': '.$this->T.'</div><button class="reply">Reply</button><button class="likeComment">';
			if ($this->L == 0){$toPrint = $toPrint.'Like';}
			else {$toPrint = $toPrint.$this->L;}
			
			$toPrint = $toPrint.'<button class="hateComment">';
			if ($this->H == 0){$toPrint = $toPrint.'Hate';}
			else {$toPrint = $toPrint.$this->H;}
			
			$toPrint = $toPrint.'</button><button class="deleteComment">Delete</button>';
			if (count($this->R)>=1)
			{
				foreach ($this->R as $commentt) 
				{
					$res = $commentt->getPrinted($bias+1);
					$toPrint = $toPrint.$res;
				}
			}
			$toPrint = $toPrint.'</div>';
			return $toPrint;
		}
		else
		{	
			if (count($this->R)>=1)
			{
				foreach ($this->R as $commentt) 
				{
					$toPrint = $commentt->getPrinted($bias+1, $specificID);
					return $toPrint;
				}
			}
		}
	}
	
	public function setMark($commentID, $markType)
	{
		$done = False;
		if ($this->I == $commentID)
		{
			if ($markType == "L")
			{ $this->L +=1; }			
			else if ($markType == "H")
			{ $this->H +=1; }
			$done = True; return $done;
		}
		else
		{
			foreach ($this->R as $comment)
			{
				$DoneHere = $comment->setMark($commentID, $markType);
				if ($DoneHere == True){$done = True; return $done;}
			}
			return $done;
		}
	}
	
	
	public function addReply($parrentCommentID, $Comment)
	{
		$done = False;
		if ($this->I == $parrentCommentID)
		{
			array_push($this->R,$Comment);
			$done = True; return $done;
		}
		else
		{
			foreach ($this->R as $comment)
			{
				$DoneHere = $comment->addReply($parrentCommentID, $Comment);
				if ($DoneHere == True){$done = True; return $done;}
			}
			return $done;
		}
	}
	
		


	public function DeleteComment($DeleteId)
	{	
		$done = False;
		foreach ($this->R as $comment)
		{
			if($comment->giveID() == $DeleteId)
			{
				$key = array_search($comment, $this->R);
				unset($this->R[$key]);
				$done = True; 
				return $done;
			}
			else{
				$innerDeleted = $comment->DeleteComment($DeleteId);
				if($innerDeleted == True){ $done = True; return $done; }
			}
		}
		return $done;
	}
	
	
}



class commentSection
{
	public $comments = array();
	public $lastCommentIndex = 1;
	
	public function addComment(string $commentTEXT, string $authorLogin, int $parrentCommentID = 0)
	{	//parrentCommentID = 0 значит в корень, остальные варианты указывают родителя
		$this->lastCommentIndex+=1;
		$dateTime = date('Y/m/d - H:i:s');
		
		$newComment = new Comment($this->lastCommentIndex, $commentTEXT, $dateTime, $authorLogin);
		$done = False;
		if ($parrentCommentID == 0)
		{
			array_push($this->comments,$newComment);
			$done = True; return $done;
		}
		else
		{
			foreach ($this->comments as $comment)
			{
				$DoneHere = $comment->addReply($parrentCommentID, $newComment);
				if ($DoneHere == True){$done = True; return $done;}
			}
			return $done;
		}
		return $lastCommentIndex;
	}

	
	public function getPrinted($specificID = 0)
	{	
		$toPrint ='';
		foreach ($this->comments as $comment)
		{	
			$toPrint = $toPrint.$comment->getPrinted($specificID = 0);
		}
		return $toPrint;
	}
	
	public function setMark($commentID, $markType)
	{
		$done = False;
		foreach ($this->comments as $comment)
		{
			$DoneHere = $comment->setMark($commentID, $markType);
			if ($DoneHere == True){$done = True; return $done;}
		}
		return $done;
	}
	
	
	public function DeleteComment($DeleteId)
	{	
		$done = False;
		$key = 0;
		foreach ($this->comments as $comment)
		{	
			if($comment->giveID() == $DeleteId)
			{
				$key = array_search($comment, $this->comments);
				unset($this->comments[$key]);
				$done = True; 
				return $done;
			}
			else{
				$innerDeleted = $comment->DeleteComment($DeleteId);
				if($innerDeleted == True){ $done = True; return $done; }
			}
		}
		return $done;

	}
	
	function sortArrBy($sortingArray, $sortColumn)
	{
		$result = [];
		foreach ($sortingArray as $row) 
		{
			$result[$row->{$sortColumn}] = $row;
		}
		ksort($result);
		return $result;
	}//$sortedUsers = $this->sortArrBy($this->comments , 'math');
}

class commentManager {
	public $comments;
	
	public function defaultSection()
	{
		$this->comments= new commentSection();
		$this->comments->addComment("комментарий в корне", "Виталя",0);
		$this->comments->addComment("Ещё комментарий", "Федор",0);
		$this->comments->addComment("ответ", "Иван",2);
	}
	
	
	public function addComment(string $commentTEXT, string $authorLogin, int $parrentCommentID)//parrentCommentID = 0 значит в корень, остальные варианты указывают  конкретного родителя
	{	
		$newCommentIndex = $this->comments->addComment($commentTEXT, $authorLogin, $parrentCommentID);
		return $newCommentIndex;
	}
	
	public function getPrinted($specificID = 0)
	{
		return $this->comments->getPrinted($specificID = 0);
	}
	
	public function setMark($commentID, $markType)
	{
		$this->comments->setMark($commentID, $markType);
	}
	
	public function DeleteComment($DeleteId)
	{	
		return $this->comments->DeleteComment($DeleteId);
	}
	
	public function save()
	{	
		$comments = serialize($this->comments);
		file_put_contents('serverSavedComments.txt', $comments);
	}
	public function retrieve()
	{	
		$s = file_get_contents('serverSavedComments.txt'); 
		$this->comments= unserialize($s);
	}
}


if(isset($_POST))
{
	$Data = $_POST['Data'];
	$Data = json_decode($Data, true);
	$toAnswer = array();
	
	switch ($Data["task"]) {
		case "giveComments":
			$comments = new commentManager();
			// $comments -> defaultSection();
			// $comments -> save();
			$comments -> retrieve();
			$commentsBlock = $comments -> getPrinted();
			$toAnswer = array("commentsBlock"=>$commentsBlock);
			break;
		case "giveThisComment":
			$comments = new commentManager();
			$comments -> retrieve();
			$commentsBlock = $comments -> getPrinted();
			$toAnswer = array("commentsBlock"=>$commentsBlock);
			break;
		case "addComment":
			$comments = new commentManager();
			$comments -> retrieve();
			$authorLogin = "authorLogin";// Берем из сессии
			$newCommentIndex = $comments -> addComment($Data["commentText"], $authorLogin, $Data["parrentCommentID"]);
			$commentsBlock = $comments -> getPrinted();
			$toAnswer = array("commentsBlock"=>$commentsBlock);
			$comments -> save();
			break;
		case "setMark":
			$comments = new commentManager();
			$comments -> retrieve();
			$comments -> setMark($Data["commentID"],$Data["markType"]);
			$commentsBlock = $comments -> getPrinted();
			$toAnswer = array("commentsBlock"=>$commentsBlock);
			$comments -> save();
			break;		
		case "deleteComment":
			$comments = new commentManager();
			$comments -> retrieve();
			$key = $comments -> DeleteComment($Data["commentID"]);
			$commentsBlock = $comments -> getPrinted();
			$toAnswer = array("commentsBlock"=>$commentsBlock);
			$comments -> save();
			break;
		default:
			$toAnswer = array("commentsBlock"=>"Неверный запрос");
	}
	echo json_encode($toAnswer).'|||';
}
?>
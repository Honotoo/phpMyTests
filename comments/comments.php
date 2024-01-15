
<?php
echo "Готовая система комментариев. 
На клиентской стороне все действия обрабатываются функциями в JS. На серверной стороне комментарии страницы объекты. 
Их можно сериализовать или сохранить в формате дерева и использовать ORM <br><br><br>";
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="commentStyle.css">
	<script src="CommentScript.js"></script>
</head>
<body>
		  
<div class="container">
    <textarea id="comment"></textarea>
    <button id="addComment">ADD</button>
    <div id="allComments">
	</div>
</div>

</body>
</html>

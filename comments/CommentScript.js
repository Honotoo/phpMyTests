
async function doQuery(Data, callBackOnDone)
{
	let formData = new FormData(); 	
	formData.append("Data", JSON.stringify(Data));
	try {
		let response = await fetch('commentServer.php', {
			method: "POST",
			body: formData,
		});
		response.text().then(function(data) { 
			let serverAnswer = JSON.parse(data.split('|||')[0]); 
			
			callBackOnDone(serverAnswer);
		});
	} catch(error) {
		console.log("error:" + error); 
	}
}

function placeComments_CB(serverAnswer)
{
	document.getElementById('allComments').innerHTML = serverAnswer["commentsBlock"];
}



window.onload = function getComments() {
	Data = {"task" : "giveComments"};
	doQuery(Data, placeComments_CB);
	document.getElementById('addComment').addEventListener('click', function (ev) {addComment(ev);});
	document.getElementById('allComments').addEventListener('click', function (ev) {makeCommentThings(ev)});
};


function hasClass(elem, className) {
    return elem.className.split(' ').indexOf(className) > -1;
}

function addComment(ev, parrentCommentID = 0) {
	console.log("addComment");
	if(hasClass(ev.target.parentElement, 'container'))
	{
		let wrapDiv, commentText;
		commentText = document.getElementById('comment').value;
		Data = {"task" : "addComment", "commentText" : commentText, "parrentCommentID" : parrentCommentID };
		doQuery(Data, placeComments_CB);
	}
	else {
		let wrapDiv, commentText;
		commentText = ev.target.parentElement.firstElementChild.value;
		parrentCommentID = ev.target.parentElement.parentElement.id;
		Data = {"task" : "addComment", "commentText" : commentText, "parrentCommentID" : parrentCommentID };
		doQuery(Data, placeComments_CB);
    }
}




function makeCommentThings(e) {
    if (hasClass(e.target, 'reply')) {
        const parentDiv = e.target.parentElement;
        const wrapDiv = document.createElement('div');
        wrapDiv.style.marginLeft = (Number.parseInt(parentDiv.style.marginLeft) + 15).toString() + 'px';
        wrapDiv.className = 'wrapper';
        const textArea = document.createElement('textarea');
        textArea.style.marginRight = '20px';
        const addButton = document.createElement('button');
        addButton.className = 'addReply';
        addButton.innerHTML = 'Add';
        const cancelButton = document.createElement('button');
        cancelButton.innerHTML = 'Cancel';
        cancelButton.className='cancelReply';
        wrapDiv.append(textArea, addButton, cancelButton);
        parentDiv.appendChild(wrapDiv);
    }
	else if(hasClass(e.target, 'addReply')) {
        addComment(e);
    } else if(hasClass(e.target, 'likeComment')) {
		commentID = e.target.parentElement.id;
		Data = {"task" : "setMark", "commentID" : commentID, "markType" : "L" };
		doQuery(Data, placeComments_CB);
		
	} else if(hasClass(e.target, 'hateComment')) {
		commentID = e.target.parentElement.id;
		Data = {"task" : "setMark", "commentID" : commentID, "markType" : "H" };
		doQuery(Data, placeComments_CB);
    } else if(hasClass(e.target, 'cancelReply')) {
        e.target.parentElement.innerHTML = '';
    } else if(hasClass(e.target, 'deleteComment')) {
		commentID = e.target.parentElement.id;
		Data = {"task" : "deleteComment", "commentID" : commentID};
		doQuery(Data, placeComments_CB);
    }
}

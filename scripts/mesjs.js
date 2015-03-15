var inDraft = false;
function ajaxGet(request) {
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET",request,false);
	xmlhttp.send();
	return xmlhttp.responseText;
}
function loadBox(type) {
	var box = document.getElementById('messages');
	var layout; var boxtype;
	
	switch(type) {
		case "inbox":
			boxtype = "readInbox";
			inDraft = false;
			break;
		case "outbox":
			boxtype = "readOutbox";
			inDraft = false;
			break;
		case "draft":
			boxtype = "readDrafts";
			inDraft = true;
			break;
		default:
			boxtype = "readInbox";
			break;
	}
	
	var data = ajaxGet("messages-ajax.php?method=AJAX&mode="+boxtype);
	data = JSON.parse(data);
	
	box.innerHTML = "";
	
	if(data.hasError) {
		box.innerHTML = data.hasError;
	} else {
		document.getElementById('mes_type').innerHTML = data[0].boxname;
		document.getElementById('mes_subj').innerHTML = data[0].col_subj;
		document.getElementById('mes_fromto').innerHTML = data[0].col_fromto;
		document.getElementById('mes_time').innerHTML = data[0].col_date;
		layout = data[0].tpl;
		for(i=1;i<data.length;i++) {
			if(isIE) {
				setMessage(box,data[i],layout);
			} else {
				setTimeout(setMessage,75*i,box,data[i],layout);
			}
		}
	}
}
function setMessage(container,data,layout) {
	layout = layout.replace(/%id%/g,data.id);
	layout = layout.replace("%subject%",data.msgSubject);
	layout = layout.replace("%sid%",data.sid);
	layout = layout.replace(/%from%/g,data.from);
	layout = layout.replace("%time%",data.time);
	container.innerHTML += layout;
	if(data.viewed == 1 && data.isowner == true) {
		document.getElementById("mes_"+data.id).style.fontWeight="bold";
	}
}

function readMessage(id) {
	if(inDraft == true) {
		compose("",id);
	} else {
		var box = document.getElementById('messages');
		var data = ajaxGet("/messages-ajax.php?method=AJAX&mode=readMessage&id="+id);
		data = JSON.parse(data);
		if(data.hasError) {
			box.innerHTML = data.hasError;
		} else {
			document.getElementById('mes_type').innerHTML = data.boxname;
			document.getElementById('mes_subj').innerHTML = data.subject;
			document.getElementById('mes_fromto').innerHTML = "<a href='user/"+data.author+"'>"+data.author+"</a>";
			document.getElementById('mes_time').innerHTML = data.date;
			
			box.innerHTML = data.tpl;
		}
	}
}

function compose(to,draft) {
	var data = ajaxGet("/messages-ajax.php?method=AJAX&mode=compose");
	data = JSON.parse(data);
	var box = document.getElementById('messages');
	if(data.hasError) {
		box.innerHTML = data.hasError;
	} else {	
		document.getElementById('mes_type').innerHTML = data.boxname;
		document.getElementById('mes_subj').innerHTML = "";
		document.getElementById('mes_fromto').innerHTML = "";
		document.getElementById('mes_time').innerHTML = "";
		box.innerHTML = data.tpl;
		
		if(draft) {
			var data = ajaxGet("messages-ajax.php?method=AJAX&mode=getDraft&id="+draft);
			data = JSON.parse(data);
			if(!data.hasError) {
				document.getElementById('mes_draftid').value = data.id;
				document.getElementById('mes_to').value = data.to;
				document.getElementById('mes_subject').value = data.msgSubject;
				document.getElementById('mes_message').value = data.msgBody;
			}
		} else if(to) {
			document.getElementById('mes_to').value=to;
		}
	}
}

function sendmessage(save) {
	var mode = (save) ? "doSave" : "doSend";
	var to = document.getElementById('mes_to').value;
	var subject = document.getElementById('mes_subject').value;
	var message = document.getElementById('mes_message').value;
	var data = ajaxGet("/messages-ajax.php?method=AJAX&mode="+mode+"&to="+to+"&subj="+subject+"&mes="+encodeURIComponent(message));
	data = JSON.parse(data);
	if(data.hasError) {
		alert(data.hasError);
	} else {
		if(inDraft == true) {
			var id = document.getElementById('mes_draftid').value;
			ajaxGet("/messages-ajax.php?method=AJAX&mode=delete&id="+id);
		}
		window.location.href="/pm";
	}
}

function delmessage(id) {
	var yesno = confirm("Are you sure you want to delete this message?");
	if(yesno) {
		var data = ajaxGet("/messages-ajax.php?method=AJAX&mode=delete&id="+id);
		data = JSON.parse(data);
		if(data.hasError) {
			alert(data.hasError);
		} else {
			window.location.href="/pm";
		}
	}
}
function replymes(id,subj) {
	location.href="/pm/compose/"+id+"/"+subj;
}
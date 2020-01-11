
 //////////////////////////////////
/////    AJAX FILEBROWSER    /////









////  show or hide the hiddentoolsdiv  //// 
function showhiddentoolsdiv(){
if (document.getElementById('hiddentoolsdiv').style.display=="none"){
		$('#hiddentoolsdiv').show();
	}
	else {
		document.getElementById('hiddentoolsdiv').style.display="none";
	}
}

function openw(ziel,name) {
	var WinW,WinH,x,y;
	WinW = 1230;
	WinH = 850;
	x=(screen.width/2-(WinW/2));
	y=(screen.height/2-(WinH/2));
	window.open(ziel, name, "toolbar=no, menubar=no, scrollbars=yes, status=no,locationbar=no, height=" + WinH +",width="+ WinW +",left=" + x +",top="+ y);
}





var request = false;




 ////////////////////////////////////////////////
/////    AJAX request to open the Editor   ///// set session var "htmlfile" and open editor

	function openEditor(htmlfile) {
		if (window.XMLHttpRequest) {			// Request erzeugen
			folderlistrequest = new XMLHttpRequest(); 	// Mozilla, Safari, Opera
		} else if (window.ActiveXObject) {
			try {
				folderlistrequest = new ActiveXObject('Msxml2.XMLHTTP'); // IE 5
			} catch (e) {
				try {
					folderlistrequest = new ActiveXObject('Microsoft.XMLHTTP'); // IE 6
				} catch (e) {}
			}
		}
		if (!folderlistrequest) {						// überprüfen, ob Request erzeugt wurde
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "./setsessionvars.php";		//ziel url
			var action ="set";
			folderlistrequest.open('post', url, true);		// Request öffnen
			folderlistrequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');		// Requestheader senden
			folderlistrequest.send('htmlfile='+htmlfile+'&action='+action);		// Request senden
			folderlistrequest.onreadystatechange = interpret_openEditor;			// Request auswerten (interpretRequest aufrufen. ohne () sonst wird sie gleich ausgefuehrt
		}
	}

	function interpret_openEditor() {
		switch (folderlistrequest.readyState) {
			case 4:
				if (folderlistrequest.status != 200) {
					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var htmlcontent = folderlistrequest.responseText;
					openw( "./xeditor/edit.php","typex-press");
					if (document.getElementById('hiddentoolsdiv').style.display=="" ) {showhiddentoolsdiv();}
					
				}
				break;
			default:
				break;
		}
	}

  /////    AJAX request to open the Editor    /////
 /////////////////////////////////////////////////


























 /////////////////////////////////////////////////////////
/////    AJAX request for the main browser table    /////

// Request senden // auf browser spezifische eigenschaften eingehn.. IE is mal wieder anders ^^
	function setRequest(dir,tsort) {
		if (window.XMLHttpRequest) {			// Request erzeugen
			request = new XMLHttpRequest(); 	// Mozilla, Safari, Opera
		} else if (window.ActiveXObject) {
			try {
				request = new ActiveXObject('Msxml2.XMLHTTP'); // IE 5
			} catch (e) {
				try {
					request = new ActiveXObject('Microsoft.XMLHTTP'); // IE 6
				} catch (e) {}
			}
		}

		if (!request) {						// überprüfen, ob Request erzeugt wurde
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "./filebrowser_maintable.php";		//ziel url
			request.open('post', url, true);		// Request öffnen
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');		// Requestheader senden
			request.send('folder_tmp='+dir+'&tsort='+tsort);		// Request senden
			request.onreadystatechange = interpretRequest;			// Request auswerten (interpretRequest aufrufen. ohne () sonst wird sie gleich ausgefuehrt
			
		}
	}
	




// Request auswerten // wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen  (4 = complete)
	function interpretRequest() {
		switch (request.readyState) {
			case 4:
				if (request.status != 200) {
					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var content = request.responseText;
					document.getElementById('ajaxcontent').innerHTML = content;  		// den Inhalt des Requests in das <div> schreiben
					window.setTimeout("initialize();", 200);  //ladet das dragndrop skript
					window.setTimeout("preview();", 200);  //ladet das preview skript
					
				}
				break;
			default:
				break;
		}
	}

  /////    AJAX request for the main browser table    /////
 /////////////////////////////////////////////////////////






 /////////////////////////JQUERY/////////////////////////////

/////    AJAX request for the left folderlist   /////
function folderlistRequest(dir){
	$.ajax({
		type: "POST",
		url: "./filebrowser_folderlist.php",
		data: { folder_tmp: dir},
		success: function(data) {
			$('#menu').html(data);
		}
	});
}
 /////    AJAX request for the left folderlist    /////
//////////////////////////////////////////////////////



///// AJAX request for the initiate operation dialog   /////
function operations(dir, type, newitem){
	$.ajax({
		type: "POST",
		url: "./filebrowser_functions_initiate.php",
		data: { ordner: dir, typ: type, newitem:newitem, name:name },
		success: function(data) {
			$('#toolscontent').html(data);
			$('#hiddentoolsdiv').show('fast');
		}
	});
}
 ///// AJAX request for the initiate operation dialog   /////
////////////////////////////////////////////////////////////



function functions_doitRequest(dir,typ,inhalt,newitem,wert) {
	var name =  $('#foobar').val();  //holt sich den wert des input textfeldes
	var extension = "";
	if ($('#foo')) {
		var extension = $('#foo').val();   //holt sich den wert des extensionfeldes
	}
	if (inhalt!="no"){    //wenn aktion gecanceled  - warum ???
		window.setTimeout("setRequest('"+wert+"');folderlistRequest('"+wert+"');", 200);
	}
	
	
	$.ajax({
		type: "POST",
		url: "./filebrowser_functions.php",
		data: { ordner: dir, typ: typ,inhalt:inhalt, newitem:newitem, wert:wert, name:name, extension:extension },
		success: function(data) {
			$('#toolscontent').html(data);
			$('#hiddentoolsdiv').fadeOut(2000);
		}
	});
}







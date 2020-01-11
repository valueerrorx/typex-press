 ////////////////////////////////////
/////// functions filebrowser //////



/////////////////////////////////////////////////////////////
///////////  macht ein element unsichbar/sichtbar ////////////
function toggle(divid) {
	if ( $(divid).is(':hidden')){$(divid).show("fast");}else{ $(divid).hide("fast");}
}
///////////  macht ein element unsichbar/sichtbar ////////////
/////////////////////////////////////////////////////////////








function logout( name, path, domain ) {
document.cookie = name + "=" +
( ( path ) ? ";path=" + path : "") +
( ( domain ) ? ";domain=" + domain : "" ) +
";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}



function thisreload()
{
setTimeout("this.location.reload(true);", 0);
// setTimeout("toggleDesignmode();", 500);
}




function disable(ziel){
	if (document.getElementById(ziel).disabled==false){
	document.getElementById(ziel).disabled=true;
	}
}
function enable(ziel){
	if (document.getElementById(ziel).disabled==true){
	document.getElementById(ziel).disabled=false;
	}
}




function showsub(sub) {
	if (document.getElementById(sub).style.display=="none")
	{document.getElementById(sub).style.display="";}
	else {document.getElementById(sub).style.display="none";}
}

function showsubonly(sub) {
	if (document.getElementById(sub).style.display=="none")
	{document.getElementById(sub).style.display="";}
}


function openuploadwindow(ziel,name) {
    var WinW,WinH,x,y;
    WinW = 870;
    WinH = 600;
    x=0;
    y=0;
    window.open(ziel, name, "toolbar=no, menubar=no, scrollbars, location=no, height=" + WinH +",width="+ WinW +",left=" + x +",top="+ y);
}





/// dragndrop script /// for dragndrop folders and dragndrop toolsdiv - distinguished by dragID

var mouseOffset = null;
var dragObject  = null;
var dragelementX = null;
var dragelementY = null;

function getPosition(e){  
	    var left = 0;  
	    var top  = 0;  
	    while (e.offsetParent){  
	        left += e.offsetLeft;  
	        top  += e.offsetTop;  
	        e     = e.offsetParent;  
	    }  
	    left += e.offsetLeft;  
	    top  += e.offsetTop;  
	    return {x:left, y:top};  
	}  



function mouseCoords(ev){
	ev = ev || window.event;
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}

function getMouseOffset(target, ev){
	ev = ev || window.event;
	var docPos    = getPosition(target);
	var mousePos  = mouseCoords(ev);
	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
}

function mouseMove(ev){
	var mousePos = mouseCoords(ev);
	if(dragObject){
		var check = dragObject.getAttribute('dragId');  //für ganz normale move funktion mit draghandle div (siehe toolsdiv)
		if(check=="draghandle")
		{
			rootParent    = dragObject.parentNode;
			rootParent.style.top      = mousePos.y - mouseOffset.y;
			rootParent.style.left     = mousePos.x - mouseOffset.x;
		}else{
			dragObject.style.cssText = 'position:absolute;border:1px dotted #CCCCCC;';
			dragObject.style.top      = mousePos.y -40;  //setzt den div 50px rauf um #content position auszugleichen und neben den zeiger damit er nicht geklickt wird beim loslassen und darunterliegende elemente ansprechbar bleiben
			dragObject.style.left     = mousePos.x + 5;
		}
	}
}


function mouseUp(ev){
	if(dragObject != undefined){  //wenn objekt am dropcontainer (alle elemente mit dem namen "droptarget")  (opera is eh guat ^^) 
		ev           = ev || window.event;
		var mousePos = mouseCoords(ev);
		var check = dragObject.getAttribute('dragId');
		if(check!="draghandle")
		{
			var alldroptargets = document.getElementsByClassName('folderlistfolder');
			for(var j=0; j < alldroptargets.length; j++){
				var targObj = document.getElementsByClassName('folderlistfolder')[j];
				var objPos  = getPosition(targObj);
				if((mousePos.x > objPos.x) && (mousePos.y > objPos.y) && (mousePos.x<(objPos.x+parseInt(targObj.offsetWidth))) && (mousePos.y<(objPos.y+parseInt(targObj.offsetHeight)))){
					var ordner = targObj.getAttribute('dragId');
					var newitem = dragObject.getAttribute('dragId');
				dragObject.style.cssText = 'border:1px dotted #CCCCCC;';
			//	document.getElementById('ausgabe').innerHTML += "Ziel: "+folder+" item "+file;
					 var typ = "copy";
					 operations(ordner,typ,newitem);
				}else{     //zuruecksetzen !!!! 
					dragObject.removeAttribute('style');
					dragObject.style.cssText = "";
				}
			}	
		}
	}
	dragObject = null;
}
document.onmousemove = mouseMove;
document.onmouseup   = mouseUp;

function initialize(what,whatelse){
	var alldragelements = document.getElementsByClassName('folderlistfile');
	for(var i=0; i < alldragelements.length; i++){
		thisitem = document.getElementsByClassName('folderlistfile')[i]; 
		thisitem.onmousedown = function(ev){
			dragObject  = this;
			mouseOffset = getMouseOffset(this, ev);
			return false;
		}
	}
}



function preview(){
	$(".imagetype").imgPreview({
			containerID: 'imgPreview',
			distanceFromCursor: {
				top: -30,
				left: 20
			},
	});
	
	$(".imagetype").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true
	});
	
	
}












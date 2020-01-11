function toggledisplay(ThisID){
	if(document.getElementById(ThisID).style.display=="none"){
		$("#"+ThisID+"").show('fast');
	}else{
		$("#"+ThisID+"").hide('fast');
	}
}



function clock() {
	var now = new Date();
	var getHour = now.getHours();
	document.getElementById('clockDiv').innerHTML=getHour;
	setTimeout('clock()',1000);
}
			
$(function(){
	clock();
});
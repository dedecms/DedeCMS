var oScrollid = "scrollnews";
var oScrollMsgid = "scrollmsg";
var isStoped = false;   
var preTop = 0;   
var curTop = 0;   
var stopTime = 0;  
 
function scroll(){
	try{
		with(document.getElementById(oScrollid)){
			noWrap = true;
		}
		document.getElementById(oScrollid).onmouseover = new Function('isStoped = true');
		document.getElementById(oScrollid).onmouseout = new Function('isStoped = false');  
		document.getElementById(oScrollid).appendChild(document.getElementById(oScrollMsgid).cloneNode(true));  
		init_srolltext();   
	}catch(e){}
}

function init_srolltext(){ 
	setTimeout(function(){document.getElementById(oScrollid).scrollTop=0;},0);
	//oScroll.scrollTop = 0;
	setInterval('scrollUp()', 15);
}   

function scrollUp(){   
	if(isStoped) return;   
	curTop += 1;
	if(curTop == 24) {   
		stopTime += 1;
		curTop -= 1;
		if(stopTime == 180) {   
			curTop = 0;
			stopTime = 0;   
		}   
	}else{   
		preTop = document.getElementById(oScrollid).scrollTop;   
		document.getElementById(oScrollid).scrollTop += 1;   
		if(preTop == document.getElementById(oScrollid).scrollTop){
			document.getElementById(oScrollid).scrollTop = 0;   
			document.getElementById(oScrollid).scrollTop += 1;   
		} 
	}
}
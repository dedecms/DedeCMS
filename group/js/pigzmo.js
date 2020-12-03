function pigzmo(menu,list,length,mousetime){
	var menus=getObject(menu).getElementsByTagName("a");
	var lists=getObject(list).getElementsByTagName("dd");
	var listname=list;
	for(var oi=0;oi<lists.length;oi++){
		lists[0].style.display="none";
	}
	if(ReadCookie(listname)){
		SetContent(ReadCookie(listname),length,menus,lists,listname);
	}else{
		SetContent("0",length,menus,lists,listname);
	}
	for(var oi=0;oi<length;oi++){
		MouseAction(oi,length,menus,lists,listname,mousetime);
	}
}

function MouseAction(c,cl,menus,lists,listname,mousetime){
	var waitInterval;
	menus[c].onclick=function(){
		SetContent(c,cl,menus,lists,listname);
	}
	menus[c].onmouseover=function(){
		ws_c=c;
		ws_cl=cl;
		ws_menus=menus;
		ws_lists=lists;
		ws_listname=listname;
		clearTimeout(waitInterval);
		waitInterval=window.setTimeout("SetContent(ws_c,ws_cl,ws_menus,ws_lists,ws_listname);",mousetime);
	}
	menus[c].onmouseout=function(){
		clearTimeout(waitInterval);
	}
}

function SetContent(c,cl,menus,lists,listname){
	getObject(listname).getElementsByTagName("dt")[0].style.display="none";
	if (lists[c].style.display=="block"){
		menus[c].blur();
		return;
	}
	for(var mi=0;mi<cl;mi++){
		menus[mi].className="";
	}
	menus[c].className="thisclass";
	menus[c].blur();
	for(var li=0;li<cl;li++){
		lists[li].style.display="none";
	}
	lists[c].style.display="block";
	SetCookie(listname,c,1000000);
}

function SetCookie(name,value,expires){
	var exp=new Date();
	exp.setTime(exp.getTime()+expires*60000);
	document.cookie=name+"="+escape(value)+";expires="+exp.toGMTString();//+";domain=pigz.cn;path=/";
} 

function ReadCookie(name){
	var oRegex=new RegExp(name+'=([^;]+)','i');
	var oMatch=oRegex.exec(document.cookie);
	if(oMatch&&oMatch.length>1)return unescape(oMatch[1]);
	else return '';
}

function getObject(objectId) {
    if (document.getElementById && document.getElementById(objectId)) {
        return document.getElementById(objectId);
    } else if (document.all && document.all(objectId)) {
        return document.all(objectId);
    } else if (document.layers && document.layers[objectId]) {
        return document.layers[objectId];
    } else {
        return false;
    }
}
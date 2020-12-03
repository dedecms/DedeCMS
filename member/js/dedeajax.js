<!--
function DedeAjax(WiteOKFunc){ //WiteOKFunc 为异步状态处理函数 

//xmlhttp和xmldom对象
this.xhttp = null;
this.xdom = null;

//post或get发送数据的键值对
this.keys = Array();
this.values = Array();
this.keyCount = -1;

//http请求头
this.rkeys = Array();
this.rvalues = Array();
this.rkeyCount = -1;

//初始化xmlhttp
if(window.XMLHttpRequest){//IE7, Mozilla ,Firefox 等浏览器内置该对象
     this.xhttp = new XMLHttpRequest();
}else if(window.ActiveXObject){//IE6、IE5
     try { this.xhttp = new ActiveXObject("Msxml2.XMLHTTP");} catch (e) { }
     if (this.xhttp == null) try { this.xhttp = new ActiveXObject("Microsoft.XMLHTTP");} catch (e) { }
}
this.xhttp.onreadystatechange = WiteOKFunc;
//rs: responseBody、responseStream、responseXml、responseText

//以下为成员函数
//--------------------------------

//初始化xmldom
this.InitXDom = function(){
  var obj = null;
  if (typeof(DOMParser) != "undefined") { // Gecko、Mozilla、Firefox
    var parser = new DOMParser();
    obj = parser.parseFromString(xmlText, "text/xml");
  } else { // IE
    try { obj = new ActiveXObject("MSXML2.DOMDocument");} catch (e) { }
    if (obj == null) try { obj = new ActiveXObject("Microsoft.XMLDOM"); } catch (e) { }
  }
  this.xdom = obj;
};

//增加一个POST或GET键值
this.AddSendKey = function(skey,svalue){
	this.keyCount++;
	this.keys[this.keyCount] = skey;
	this.values[this.keyCount] = escape(svalue);
};

//增加一个Http请求头
this.AddHttpHead = function(skey,svalue){
	this.rkeyCount++;
	this.rkeys[this.rkeyCount] = skey;
	this.rvalues[this.rkeyCount] = svalue;
};

//清除当前对象的哈希表参数
this.ClearSet = function(){
	this.keyCount = -1;
	this.keys = Array();
	this.values = Array();
	this.rkeyCount = -1;
	this.rkeys = Array();
	this.rvalues = Array();
};

//用Post方式发送数据
this.SendPost = function(purl,ptype){
	var pdata = "";
	var httphead = "";
	var i=0;
	this.state = 0;
	this.xhttp.open("POST", purl, true); 
	
	if(this.rkeyCount!=-1){ //发送用户自行设定的请求头
  	for(;i<=this.rkeyCount;i++){
  		this.xhttp.setRequestHeader(this.rkeys[i],this.rvalues[i]); 
  	}
  }
　if(ptype=="text") this.xhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
　
  if(this.keyCount!=-1){ //post数据
  	for(;i<=this.keyCount;i++){
  		if(pdata=="") pdata = this.keys[i]+'='+this.values[i];
  		else pdata += "&"+this.keys[i]+'='+this.values[i];
  	}
  }
  this.xhttp.send(pdata);
};

} // End Class DedeAjax
-->
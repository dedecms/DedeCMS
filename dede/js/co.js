<!--

function Nav(){
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
  else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
  else return "OT";
}

function MyObj(oid)
{
	return document.getElementById(oid);
}

function ShowHide(objname)
{
   var obj = MyObj(objname);
   if(obj.style.display==null || obj.style.display=='none')
   {
   	 if(Nav()=='IE') obj.style.display = "block";
   	 else obj.style.display = "table-row";
   }
	 else {
	 	 obj.style.display = "none";
	 }
}

function ShowTestWin(surl)
{
	window.open(surl, "testWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=450,left=100, top=100");
}

function ShowItem(objname)
{
 	var obj = MyObj(objname);
 	if(Nav()=='IE') obj.style.display = "block";
 	else  obj.style.display = "table-row";
}

function TestMore()
{
	if(MyObj('usemore').checked) {
		 if(Nav()=='IE')  MyObj('usemoretr').style.display = 'block';
		 else MyObj('usemoretr').style.display = 'table-row';
		 MyObj('handset').style.display = 'none';	
	}
	else {
		MyObj('usemoretr').style.display = 'none';
		if(Nav()=='IE')  MyObj('handset').style.display = 'block';
		else MyObj('handset').style.display = 'table-row';
	}
}

function SelSourceSet()
{
	 if(MyObj('source3').checked)
	 {
		  if(Nav()=='IE') MyObj('rssset').style.display = 'block';
		  else MyObj('rssset').style.display = 'table-row';
		  MyObj('batchset').style.display = 'none';
		  MyObj('handset').style.display = 'none';
		  MyObj('arturl').style.display = 'none';
	 }else if(MyObj('source2').checked)
	 {
		  MyObj('rssset').style.display = 'none';
		  MyObj('batchset').style.display = 'none';
		  if(Nav()=='IE') MyObj('handset').style.display = 'block';
		  else MyObj('handset').style.display = 'table-row';
		  if(Nav()=='IE') MyObj('arturl').style.display = 'block';
		  else MyObj('arturl').style.display = 'table-row';
	 }
	 else
	 {
		  MyObj('rssset').style.display = 'none';
		  
		  if(Nav()=='IE') MyObj('batchset').style.display = 'block';
		  else MyObj('batchset').style.display = 'table-row';
		  
		  if(Nav()=='IE') MyObj('handset').style.display = 'block';
		  else MyObj('handset').style.display = 'table-row';
		  
		  if(Nav()=='IE') MyObj('arturl').style.display = 'block';
		  else MyObj('arturl').style.display = 'table-row';
	 }
	 TestMore();
}

function SelListenSet()
{
	if(MyObj('islisten1').checked)
	{
		MyObj('listentr').style.display = 'none';
	}
	else
	{
		if(Nav()=='IE') MyObj('listentr').style.display = 'block';
		else  MyObj('listentr').style.display = 'table-row';
	}
}

function SelUrlruleSet()
{
	if(MyObj('urlrule2').checked)
	{
		MyObj('arearuletr').style.display = 'none';
		if(Nav()=='IE') MyObj('regxruletr').style.display = 'block';
		else MyObj('regxruletr').style.display = 'table-row'; 
	}
	else
	{
		if(Nav()=='IE') MyObj('arearuletr').style.display = 'block';
		else MyObj('arearuletr').style.display = 'table-row';
		MyObj('regxruletr').style.display = 'none';
	}
}

function TestRss()
{
	var surl = '';
	surl = escape(MyObj('rssurl').value);
	ShowTestWin("co_do.php?dopost=testrss&rssurl="+surl);
}

function TestRegx()
{
	var surl = escape(MyObj('regxurl').value);
	var sstart = MyObj('startid').value;
	var send = MyObj('endid').value;
	var saddv = MyObj('addv').value;
	ShowTestWin("co_do.php?dopost=testregx&regxurl="+surl+"&startid="+sstart+"&endid="+send+"&addv="+saddv);
}

function toHex( n )
{
	var digitArray = new Array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
	var result = ''
	var start = true;

	for ( var i=32; i>0; ) {
		i -= 4;
		var digit = ( n >> i ) & 0xf;
		if (!start || digit != 0) {
				start = false;
				result += digitArray[digit];
		 }
	}
	return ( result == '' ? '0' : result );
}

function SelTrim(selfield)
{
	var tagobj = MyObj(selfield);
	if(Nav()=='IE'){ var posLeft = window.event.clientX-200; var posTop = window.event.clientY; }
      else{ var posLeft = 100;var posTop = 100; }
	window.open("templets/co_trimrule.html?"+selfield, "coRule", "scrollbars=no,resizable=yes,statebar=no,width=320,height=180,left="+posLeft+", top="+posTop);
}

-->
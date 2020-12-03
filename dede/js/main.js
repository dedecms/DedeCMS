<!--

function $Nav(){
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
	else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
	else return "OT";
}

function $Obj(objname){
	return document.getElementById(objname);
}

function ShowColor(){
	var fcolor=showModalDialog("img/color.htm?ok",false,"dialogWidth:106px;dialogHeight:110px;status:0;dialogTop:"+(+120)+";dialogLeft:"+(+120));
	if(fcolor!=null && fcolor!="undefined") document.form1.color.value = fcolor;
}

function ShowHide(objname){
	var obj = $Obj(objname);
	if(obj.style.display != "none" ) obj.style.display = "none";
	else obj.style.display = "block";
}

function ShowHideT(objname){
	var obj = $Obj(objname);
	if(obj.style.display != "none" ) obj.style.display = "none";
	else obj.style.display = ($Nav()=="IE" ? "block" : "table");
}

function ShowObj(objname){
	var obj = $Obj(objname);
	obj.style.display = ($Nav()=="IE" ? "block" : "table");
}

function ShowObjRow(objname)
{
	var obj = $Obj(objname);
	obj.style.display = ($Nav()=="IE" ? "block" : "table-row");
}

function AddTypeid2()
{
	ShowObjRow('typeid2tr');
	$Obj('typeid2ct').innerHTML = $Obj('typeidct').innerHTML.replace('typeid','typeid2');
}

function HideObj(objname){
	var obj = $Obj(objname);
	obj.style.display = "none";
}

function ShowItem1(){
	ShowObj('head1'); ShowObj('needset'); HideObj('head2'); HideObj('adset');
}

function ShowItem2(){
	ShowObj('head2'); ShowObj('adset'); HideObj('head1'); HideObj('needset');
}

function SeePic(img,f) {
	if( f.value != '' ) 	img.src = f.value;
}

function SeePicNew(imgdid,f) {
	if(f.value=='') return ;
	var objpicname = document.getElementById('picname');
	objpicname.value = f.value;
	var newPreview = document.getElementById(imgdid);
	newPreview.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = f.value;
	newPreview.style.width = '150px';
	newPreview.style.height = '100px';
}

function SelectFlash(){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-300; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	window.open("../include/dialog/select_media.php?f=form1.flashurl", "popUpFlashWin", "scrollbars=yes,resizable=yes,statebar=no,width=500,height=350,left="+posLeft+", top="+posTop);
}

function SelectMedia(fname){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-200; var posTop = window.event.clientY; }
	else{ var posLeft = 100;var posTop = 100; }
	window.open("../include/dialog/select_media.php?f="+fname, "popUpFlashWin", "scrollbars=yes,resizable=yes,statebar=no,width=500,height=350,left="+posLeft+", top="+posTop);
}

function SelectSoft(fname){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-200; var posTop = window.event.clientY-50; }
	else{ var posLeft = 100; var posTop = 100; }
	window.open("../include/dialog/select_soft.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function SelectImage(fname,stype){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	if(!fname) fname = 'form1.picname';
	if(!stype) stype = '';
	window.open("../include/dialog/select_images.php?f="+fname+"&imgstick="+stype, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function imageCut(fname) {
	if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	if(!fname) fname = 'picname';
	file = document.getElementById(fname).value;
	if(file == '') {
		alert('请先选择网站内已上传的图片');
		return false;
	}
	window.open("imagecut.php?f="+fname+"&file="+file, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=800,height=600,left="+posLeft+", top="+posTop);
}

function SelectImageN(fname,stype,vname){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	if(!fname) fname = 'form1.picname';
	if(!stype) stype = '';
	window.open("../include/dialog/select_images.php?f="+fname+"&imgstick="+stype+"&v="+vname, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function SelectKeywords(f){
	if($Nav()=='IE'){ var posLeft = window.event.clientX-350; var posTop = window.event.clientY-200; }
	else{ var posLeft = 100; var posTop = 100; }
	window.open("article_keywords_select.php?f="+f, "popUpkwWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=450,left="+posLeft+", top="+posTop);
}

function InitPage(){
	var selsource = $Obj('selsource');
	var selwriter = $Obj('selwriter');
	var titlechange = $Obj('title');
	if(selsource){ selsource.onmousedown=function(e){ SelectSource(e); } }
	if(selwriter){ selwriter.onmousedown=function(e){ SelectWriter(e); } }
	if(titlechange){ titlechange.onchange=function(e){ TestHasTitle(e); } }
}

function OpenMyWin(surl){
	window.open(surl, "popUpMyWin", "scrollbars=yes,resizable=yes,statebar=no,width=500,height=350,left=200, top=100");
}

function OpenMyWinCoOne(surl){
	window.open(surl, "popUpMyWin2", "scrollbars=yes,resizable=yes,statebar=no,width=700,height=450,left=100,top=50");
}

function PutSource(str){
	var osource = $Obj('source');
	if(osource) osource.value = str;
}

function PutWriter(str){
	var owriter = $Obj('writer');
	if(owriter) owriter.value = str;
}

function SelectSource(e){
	LoadNewDiv(e,'article_select_sw.php?t=source&k=8','_mysource');
}

function SelectWriter(e){
	LoadNewDiv(e,'article_select_sw.php?t=writer&k=8','_mywriter');
}

function LoadNewDiv(e,surl,oname)
{
	if($Nav()=='IE'){ var posLeft = window.event.clientX-20; var posTop = window.event.clientY-20; }
	else{ var posLeft = e.pageX-20; var posTop = e.pageY-20; }
	var newobj = $Obj(oname);
	if(!newobj){
		newobj = document.createElement("DIV");
		newobj.id = oname;
		newobj.style.position='absolute';
		newobj.className = "dlgws";
		newobj.style.top = posTop;
		newobj.style.left = posLeft;
		document.body.appendChild(newobj);
	}
	else{
		newobj.style.display = "block";
	}
	if(newobj.innerHTML.length<10){
		var myajax = new DedeAjax(newobj);
		myajax.SendGet(surl);
	}
}

function ShowUrlTr(){
	var jumpTest = $Obj('flagsj');
	var jtr = $Obj('redirecturltr');
	var jf = $Obj('redirecturl');
	if(jumpTest.checked) jtr.style.display = "block";
	else{
		jf.value = '';
		jtr.style.display = "none";
	}
}

function ShowUrlTrEdit(){
	ShowUrlTr();
	var jumpTest = $Obj('isjump');
	var rurl = $Obj('redirecturl');
	if(!jumpTest.checked) rurl.value="";
}

function CkRemote(){
	document.getElementById('picname').value = '';
}

function TestHasTitle(e){
	LoadNewDiv2(e,'article_test_title.php?t='+$Obj('title').value,'_mytitle',"dlgTesttitle");
}

function LoadNewDiv2(e,surl,oname,dlgcls)
{
	if($Nav()=='IE'){ var posLeft = window.event.clientX-20; var posTop = window.event.clientY-20; }
	else{ var posLeft = e.pageX-20; var posTop = e.pageY-20; }
	var newobj = $Obj(oname);
	if(!newobj){
		newobj = document.createElement("DIV");
		newobj.id = oname;
		newobj.style.position='absolute';
		newobj.className = dlgcls;
		newobj.style.top = posTop;
		newobj.style.left = posLeft;
		newobj.style.display = 'none';
		document.body.appendChild(newobj);
	}
	newobj.innerHTML = '';
	var myajax = new DedeAjax(newobj);
	myajax.SendGet2(surl);
	if(newobj.innerHTML=='') newobj.style.display = 'none';
	else newobj.style.display = 'block';
	DedeXHTTP = null;
}

-->
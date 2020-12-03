<!--
document.onreadystatechange = function(){
	if(document.readyState!="complete") return;
	else parentForm.attachEvent("onsubmit", GetContent);
}

function Load_MyEditor(){
	_MyEDoc = window.frames['_MyEditor'].document;
	_MyEDoc.designMode = "On";
	_MyEditor.focus();
}

function GetContent(){
	if(isediter==1) parentField.value=_MyEDoc.body.innerHTML;
	else parentField.value=document.form1.artbody.value;
	//return parentForm.onsubmit();
}

function GetParentValue(){
	if(_MyEDoc) _MyEDoc.body.innerHTML += parentField.value;
}

function SetContent(fvalue){
	if(isediter==1) _MyEDoc.body.innerHTML = fvalue;
	else document.form1.artbody.value = fvalue;
}

function Show_MyEditor(){
	var tedit = document.getElementById("textedit");
	var hedit = document.getElementById("htmledit");
	var mbar = document.getElementById("menubar");
	tedit.style.display = "none";
  hedit.style.display = "block";
  mbar.style.display = "block";
	_MyEDoc.body.innerHTML = document.form1.artbody.value;
	_MyEditor.focus();
	isediter = 1;
}

function ShowCode_MyEditor(){
	var tedit = document.getElementById("textedit");
  var hedit = document.getElementById("htmledit");
  var mbar = document.getElementById("menubar");
  tedit.style.display = "block";
  hedit.style.display = "none";
  mbar.style.display = "none";
  document.form1.artbody.value = _MyEDoc.body.innerHTML;
  document.form1.artbody.focus();
  isediter = 0;
}


//执行任意命令
function doExecute(command,OptionSet)
{
	_MyEditor.focus();
	_MyEDoc.execCommand(command, true, OptionSet);
	_MyEditor.focus();
}
//选择字体样式
function doFontName(fn){
	_MyEditor.focus();
	_MyEDoc.execCommand('FontName', false, fn);
	_MyEditor.focus();
}
//选择字体大小
function doFontSize(fs){
	_MyEditor.focus();
	_MyEDoc.execCommand('FontSize', false, fs);
	_MyEditor.focus();
}
//插入特定的文本
function doInsertText(ntxt)
{
	_MyEditor.focus();
	_MyEDoc.selection.createRange().pasteHTML(ntxt);
	_MyEditor.focus();
}
//插入<br>
function doInsertBr()
{
	doInsertText("<br>");
}
//插入 nbsp
function doInsertBn()
{
	doInsertText("&nbsp;");
}
//插入分页符
function doInsertSplitPage()
{
	doInsertText("#p#分页标题#e#");
}
//调用对话框插入内容
function ShowMsgboxDo(wurl,dw,dh)
{
	_MyEditor.focus();
	var reValue = showModalDialog(wurl, false, 'scroll:no;dialogWidth:'+dw+'px;dialogHeight:'+dh+'px;status:0;');
	if (reValue!=undefined){
		_MyEDoc.selection.createRange().pasteHTML(reValue);
		_MyEditor.focus();
	}
	else{
		_MyEditor.focus();
		return false;
	}
}
//插入图象
function doInsertImage(){
	ShowMsgboxDo("image.php?"+Date(),460,420);
}
//插入Flash
function doInsertFlash(){
	ShowMsgboxDo("flash.htm?"+Date(),365,150);
}
//插入图象
function doInsertImageUser(){
	ShowMsgboxDo("imageuser.php?"+Date(),460,420);
}
//插入Flash
function doInsertFlashUser(){
	ShowMsgboxDo("flashuser.htm?"+Date(),365,150);
}
//插入多媒体文件
function doInsertMedia(){
	ShowMsgboxDo("media.htm?"+Date(),365,180);
}
//选择颜色
function doFontColor(){
	_MyEditor.focus();
	var fcolor=showModalDialog("color.htm?"+Date(),false,"scroll:no;dialogWidth:300px;dialogHeight:280px;status:0;");
	_MyEDoc.execCommand('ForeColor',false,fcolor);
	_MyEditor.focus();
}
//插入表格
function doInsertTable(){
	ShowMsgboxDo("table.htm?"+Date(),330,200);
}
//插入附件
function doInsertAddon(){
	ShowMsgboxDo("addon.php?"+Date(),450,120);
}
//插入引用
function doInsertQuote()
{
	var quoteString = "<table style='border-right: #cccccc 1px dotted; table-layout: fixed; border-top: #cccccc 1px dotted; border-left: #cccccc 1px dotted; border-bottom: #cccccc 1px dotted' cellspacing=0 cellpadding=6 width='95%' align=center border=0>\r\n";
  quoteString += "<tr><td style='word-wrap: break-word' bgcolor='#fdfddf'>\r\n<font color='#FF0000'>以下为引用的内容：</font><br>\r\n";
  quoteString += "</td></tr></table>\r\n";
  doInsertText(quoteString);
}
//插入组分栏框
function doInsertGroup()
{
  ShowMsgboxDo("group.htm?"+Date(),350,160);
}
//粘贴从word里复制的文本
function PasteWord()
{
  _MyEditor.focus();
  var whtml = showModalDialog('word.htm?'+Date(), false, 'scroll:yes;dialogWidth:520px;dialogHeight:240px;status:0;');
	if(whtml==undefined) return;
  whtml = whtml.replace(/<\/?SPAN[^>]*>/gi, "" );
	whtml = whtml.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3");
	whtml = whtml.replace(/<(\w[^>]*) style="([^"]*)"([^>]*)/gi, "<$1$3");
	whtml = whtml.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3");
	whtml = whtml.replace(/<\\?\?xml[^>]*>/gi, "");
	whtml = whtml.replace(/<\/?\w+:[^>]*>/gi, "");
	whtml = whtml.replace(/<FONT face[^>]*>/gi, "");
	whtml = whtml.replace(/<\/FONT><\/FONT>/gi, "</FONT>");
	whtml = whtml.replace(/<P><\/FONT><\/P>/gi, "<BR/>");
	doInsertText(whtml);
	_MyEditor.focus();
}
//生成锚点标签
function doInsertAnchor()
{
	_MyEditor.focus();
	var sAnchorName = window.prompt("请输入锚点名称！","AnchorName");
	sAnchorName = "<a name='"+sAnchorName+"'></a>";
	_MyEDoc.selection.createRange().pasteHTML(sAnchorName);
	_MyEditor.focus();
}
-->
<?
require("config.php");
require("inc_typelink.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦Html编辑器</title>
<link href="htmledit/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
var doc;
var winsize;
//网页载入时调用这个函数-------
function LoadEditor()
{
	doc=document.frames.Editor.document;
	doc.designMode = "On";
	Editor.focus();
	if(document.all)
	{
		document.all.menuBar.style.width=(screen.width-200)+"px";
		document.all.Editor.style.width=(screen.width-200)+"px";
		document.all.myView.style.width=screen.width-200;
		document.all.artbody.style.width=(screen.width-200)+"px";
		winsize=0;
	}
}
function resideEditor()
{
	if(winsize==0){
		document.all.menuBar.style.width=(screen.width-80)+"px";
		document.all.Editor.style.width=(screen.width-80)+"px";
		document.all.myView.style.width=screen.width-80;
		document.all.artbody.style.width=(screen.width-80)+"px";
		winsize=1;
	}
	else
	{
		document.all.menuBar.style.width=(screen.width-220)+"px";
		document.all.Editor.style.width=(screen.width-220)+"px";
		document.all.myView.style.width=screen.width-220;
		document.all.artbody.style.width=(screen.width-220)+"px";
		winsize=0;
	}
}
function ShowEditor()
{
	document.all.myView.style.visibility="visible";
	doc.body.innerHTML = document.form1.artbody.value;  
	document.all.myCode.style.visibility="hidden";  
	Editor.focus();
}
function ShowCodeEditor()
{
	document.all.myView.style.visibility="hidden";
    document.all.myCode.style.visibility="visible";
    document.form1.artbody.value = doc.body.innerHTML;
    document.form1.artbody.focus();
}
function ClearContent()
{
	document.form1.artbody.value = "";
	doc.body.innerHTML = "";
}
function doFontName(fn){
	doc.execCommand('FontName', false, fn);
	Editor.focus();
}
function doFontSize(fs){
	doc.execCommand('FontSize', false, fs);
	Editor.focus();
}
function doFontColor(){
	var fcolor=showModalDialog("htmledit/color.htm",false,"dialogWidth:300px;dialogHeight:280px;status:0;");
	doc.execCommand('ForeColor',false,fcolor);
	Editor.focus();
}
function doInsertTable(){
	var dotable=showModalDialog("htmledit/table.htm",false,"dialogWidth:330px;dialogHeight:170px;status:0;");
	if (dotable!=undefined){
		doc.selection.createRange().pasteHTML(dotable);
	}
	else
	{
		return false;
	}
	Editor.focus();
}
function doInsertBr()
{
	doc.selection.createRange().pasteHTML("<br>");
	Editor.focus();
}
function doInsertBn()
{
	doc.selection.createRange().pasteHTML("&nbsp;");
	Editor.focus();
}
function doInsertImage(){
	window.open("htmledit/image.php", 'imagein', 'scrollbars=no,resizable=no,width=440,height=380,left=100, top=50,screenX=0,screenY=0');
}
function doInsertFlash(){
	var dotable=showModalDialog("htmledit/flash.htm",false,"dialogWidth:330px;dialogHeight:150px;status:0;");
	if (dotable!=undefined){
		doc.selection.createRange().pasteHTML(dotable);
	}
	else
	{
		return false;
	}
	Editor.focus();
}

function doSubmit()
{
	if(document.all.myCode.style.visibility=="hidden")
		document.form1.artbody.value = doc.body.innerHTML;
	if(document.form1.title.value=="")
	{
   		document.form1.title.focus();
   		alert("文章标题不能为空！");
   		return false;
	}
	if(document.form1.typeid.value=="0")
	{
   		document.form1.typeid.focus();
   		alert("文章必须选择一个分类！");
   		return false;
	}
	if(document.form1.artbody.value=="")
	{
   		alert("文章内容不能为空！");
   		return false;
	}
	document.form1.submit();
}
</script>
</head>
<body bgcolor="#FAFCF1" leftmargin="20" topmargin="0" onResize="resideEditor();">
<table width="600" border="0" cellspacing="0" cellpadding="0">
  <form name="form1" method="post" action="add_news_ok.php">
  <input type="hidden" name="ishtml" value="1">
    <tr> 
      <td height="120" valign="top"> 
        <!--以下与HTML编辑器无关-->
        <table width="600" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td height="2"></td>
          </tr>
          <tr> 
            <td height="22"> <table width="100%" border="0" cellspacing="0" cellpadding="0" height="22">
                <tr> 
                  <td width="5%" align="center" valign="bottom"><img src="htmledit/img/addnews.gif">&nbsp;</td>
                  <td width="60%"><strong>发布文章</strong> (需分页处插入: #p# 可将文章分多页显示) </td>
                  <td width="35%"></td>
                </tr>
              </table></td>
          </tr>
          <tr bgcolor="#cccccc"> 
            <td height="1"></td>
          </tr>
          <tr bgcolor="#ffffff"> 
            <td height="1"></td>
          </tr>
          <tr> 
            <td height="2"></td>
          </tr>
          <tr> 
            <td height="95" valign="top"> <table width="99%" border="0" align="right" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="12%" height="22" nowrap>文章标题：</td>
                  <td width="38%"><input name="title" type="text" id="title" size="28"></td>
                  <td width="12%" nowrap>文章选项：</td>
                  <td width="38%"> <input type="checkbox" name="isdd[]" id="isdd" value="1">
                    图片&nbsp; <input type="checkbox" name="redtitle[]" id="redtitle" value="1">
                    推荐 </td>
                </tr>
                <tr> 
                  <td height="22">文章来源：</td>
                  <td colspan="3"><input name="source" type="text" id="source" value="" size="20"> 
                    文章作者： <input name="writer" type="text" id="writer" value="" size="12">
                    &nbsp;时间：
                    <input name="stime" type="text" id="stime" value="<?=strftime("%Y-%m-%d")?>" size="12">
                  </td>
                </tr>
                <tr> 
                  <td height="22">文章类别：</td>
                  <td colspan="3"> <select name="typeid">
                      <?
						if(empty($typeid)) echo "<option value='0' selected>--请选择--</option>\r\n";
                    	$ut = new TypeLink();
						if($cuserLogin->GetUserChannel()<=0)
							$ut->GetOptionArray($typeid,0,1);
						else
							$ut->GetOptionArray($typeid,$cuserLogin->getUserChannel(),1);
					?>
                    </select> &nbsp; 文章级别： 
                    <select name="rank" id="rank">
                      <?
                      if($cuserLogin->getUserType()==10||$cuserLogin->getUserType()==5)
					  {
                      	$rs = mysql_query("Select * From dede_membertype where rank>=0");
                      	while($row=mysql_fetch_object($rs))
                      	{
                      		echo "<option value=\"".$row->rank."\">".$row->membername."</option>\n";
                      	}
                      }
                      else
                      	echo "<option value='-1'>原始稿件</option>\n";
                      ?>
                    </select> &nbsp;&nbsp; <input name="button" type="button" class="coolbg" style="width:80;height:22;font-size:10pt;line-height:130%" onClick="doSubmit();" value="保存文章"> 
                  </td>
                </tr>
                <tr> 
                  <td height="60">文章简述：</td>
                  <td colspan="3"><textarea name="shortmsg" cols="60" rows="3" id="shortmsg"></textarea></td>
                </tr>
                <tr> 
             		<td height="22">其它选项：</td>
             		<td colspan="3">
             		<?if($isUrlOpen) echo "<input type='checkbox' name='saveremoteimg' value='1'>远程图片本地化  ";?>
             		</td>
           		</tr>
              </table>
              </td>
          </tr>
        </table>
        <!--文章信息部份结束-->
		<input type="hidden" name="ishtml" value="1">
      </td>
    </tr>
    <tr> 
      <td>
	  
	  <table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#E0FAAF" id="menuBar">
          <tr bgcolor="#cccccc"> 
            <td height="1" colspan="21"></td>
          </tr>
          <tr bgcolor="#ffffff"> 
            <td height="1" colspan="21"></td>
          </tr>
          <tr> 
            <td width="24" height="24"><a href="javascript:doSubmit();"><img src="htmledit/img/save.gif" alt="保存" width="22" height="22" border="0"></a></td>
            <td width="22"><img src="htmledit/img/sp.gif" width="22" height="22"></td>
            <td width="24"><a href="javascript:;" onclick="doc.execCommand('undo');"><img src="htmledit/img/redo.gif" alt="撤消" width="22" height="22" border="0"></a></td>
            <td width="24"><a href="javascript:;" onclick="doc.execCommand('redo');"><img src="htmledit/img/undo.gif" alt="重做" width="22" height="22" border="0"></a></td>
            <td width="24"><img src="htmledit/img/sp.gif" width="22" height="22"></td>
            <td width="24"><a href="javascript:doInsertImage();"><img src="htmledit/img/img.gif" alt="插入图片" width="22" height="22" border="0"></a></td>
            <td width="24"><a href="javascript:doInsertFlash();"><img src="htmledit/img/swf.gif" alt="flash" width="22" height="22" border="0"></a></td>
            <td width="24"><a href="javascript:doInsertTable();"><img src="htmledit/img/table.gif" alt="插入表格" width="22" height="22" border="0"></a></td>
            <td width="24"><a href="javascript:;" onclick="doc.execCommand('CreateLink');"><img src="htmledit/img/link.gif" alt="超链接" width="22" height="22" border="0"></a></td>
            <td width="24"><img src="htmledit/img/sp.gif" width="22" height="22"></td>
            <td width="24"><a href="javascript:doFontColor();"><img src="htmledit/img/color.gif" alt="字体颜色" width="22" height="22" border="0"></a></td>
            <td>&nbsp;</td>
            <td colspan="9"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="14%">字体：</td>
                  <td width="35%"> <select name="selectFont" id="selectFont" style="height:18px" onChange="doFontName(this[this.selectedIndex].value);this.selectedIndex=0;">
                      <option value="0">--默认--</option>
                      <option value="宋体">宋体</option>
                      <option value="黑体">黑体</option>
                      <option value="楷体_GB2312">楷体_GB2312</option>
                      <option value="Arial">Arial</option>
                      <option value="Arial Black">Arial Black</option>
                    </select></td>
                  <td width="17%">　大小：</td>
                  <td width="34%"> <select name="selectSize" id="selectSize" style="height:18px" onChange="doFontSize(this[this.selectedIndex].value);this.selectedIndex=0;">
                      <option value="0">默认</option>
                      <option value="1">1 (8磅)</option>
                      <option value="2">2 (10磅)</option>
                      <option value="3">3 (12磅)</option>
                      <option value="4">4 (14磅)</option>
                      <option value="5">5 (18磅)</option>
                      <option value="6">6 (24磅)</option>
                      <option value="7">7 (36磅)</option>
                    </select></td>
                </tr>
              </table></td>
          </tr>
          <tr bgcolor="#cccccc"> 
            <td height="1" colspan="21"></td>
          </tr>
          <tr bgcolor="#ffffff"> 
            <td height="1" colspan="21"></td>
          </tr>
          <tr> 
            <td height="24"><a href="javascript:;" onclick="doc.execCommand('JustifyLeft');"><img src="htmledit/img/left.gif" alt="左对齐" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:;" onclick="doc.execCommand('JustifyCenter');"><img src="htmledit/img/center.gif" alt="居中" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:;" onclick="doc.execCommand('JustifyRight');"><img src="htmledit/img/right.gif" alt="右对齐" width="22" height="22" border="0"></a></td>
            <td><img src="htmledit/img/sp.gif" width="22" height="22"></td>
            <td><a href="javascript:;" onclick="doc.execCommand('Underline');"><img src="htmledit/img/u.gif" alt="下划线" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:;" onclick="doc.execCommand('Bold');"><img src="htmledit/img/b.gif" alt="加粗" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:;" onclick="doc.execCommand('Italic');"><img src="htmledit/img/i.gif" width="22" height="22" border="0"></a></td>
            <td><img src="htmledit/img/sp.gif" width="22" height="22"></td>
            <td><a href="javascript:doInsertBn();"><img src="htmledit/img/nbsp.gif" alt="空格" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:doInsertBr();"><img src="htmledit/img/br.gif" alt="换行" width="22" height="22" border="0"></a></td>
            <td><a href="javascript:;" onclick="doc.execCommand('InsertHorizontalRule');"><img src="htmledit/img/hr.gif" alt="横线" width="22" height="22" border="0"></a></td>
            <td align="center"><img src="htmledit/img/sp.gif" width="22" height="22"> 
            </td>
            <td width="1">&nbsp;</td>
            <td width="26"><a href="javascript:;" onclick="doc.execCommand('Copy');"><img src="htmledit/img/copy.gif" alt="复制" width="22" height="22" border="0"></a> 
            </td>
            <td width="26"><a href="javascript:;" onclick="doc.execCommand('Paste');"><img src="htmledit/img/par.gif" alt="粘贴" width="22" height="22" border="0"></a></td>
            <td width="25"><input name="modeCheck" type="radio" value="1" checked onClick="ShowEditor();"></td>
            <td width="49">可视化</td>
            <td width="28"><input type="radio" name="modeCheck" value="0" onClick="ShowCodeEditor();"></td>
            <td width="62">编辑源码</td>
            <td>&nbsp;</td>
          </tr>
		  <tr bgcolor="#cccccc"> 
            <td height="1" colspan="21"></td>
          </tr>
          <tr bgcolor="#ffffff"> 
            <td height="1" colspan="21"></td>
          </tr>
        </table>
        
        
        </td>
    </tr>
    <tr> 
      <td height="1" colspan="19"></td>
    </tr>
    <tr> 
      <td height="1" colspan="19"></td>
    </tr>
    <tr> 
      <td height="278" valign="top"> 
        <!--这里为编辑器部份-->
        <div id="myView" style="position:absolute; left:20px; top:244px; width:620px; height:378px"> 
          <iframe id="Editor" marginwidth="1" scrolling="yes" style="height:377px;width:620px;background-color:white;"></iframe>
        </DIV>
        <div id="myCode" style="position:absolute; left:20px; top:243px; width:596px; height:378px;visibility:hidden"> 
          <textarea name="artbody" cols="65" rows="23" id="artbody" style="width:600px;height:377px;"></textarea>
        </DIV>
        <script language="JavaScript">LoadEditor();</script> 
        <!--//编辑器代码//结束-->
      </td>
    </tr>
  </form>
</table>
</body>

</html>

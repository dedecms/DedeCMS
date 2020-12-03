<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
$userChannel = $cuserLogin->getUserChannel();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>类别管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript" src="js/context_menu.js"></script>
<script language="javascript" src="js/ieemu.js"></script>
<script language="javascript" src="../include/dedeajax.js"></script>
<script language="javascript">
function LoadSuns(ctid,tid)
{
	if($(ctid).innerHTML.length < 10){
	  var myajax = new DedeAjax($(ctid));
	  myajax.SendGet('catalog_do.php?dopost=GetSunLists&cid='+tid);
  }
  else{ if(document.all) showHide(ctid); }
}
function showHide(objname)
{
   if($(objname).style.display=="none") $(objname).style.display = "block";
	 else $(objname).style.display="none";
}
if(moz) {
	extendEventObject();
	extendElementModel();
	emulateAttachEvent();
}
//普通栏目
function CommonMenu(obj,tid,tname)
{
  var eobj,popupoptions
  popupoptions = [
    new ContextItem("增加内容",function(){location="catalog_do.php?cid="+tid+"&dopost=addArchives";}),
    new ContextItem("管理内容",function(){location="catalog_do.php?cid="+tid+"&dopost=listArchives";}),
    new ContextSeperator(),
    new ContextItem("预览分类",function(){ window.open("<?=$cfg_plus_dir?>/list.php?tid="+tid); }),
    new ContextItem("更新HTML",function(){ location="makehtml_list.php?cid="+tid; }),
    new ContextItem("获取JS文件",function(){ location="catalog_do.php?cid="+tid+"&dopost=GetJs"; }),
    new ContextSeperator(),
    new ContextItem("增加子类",function(){location="catalog_add.php?ID="+tid;}),
    new ContextItem("更改栏目",function(){location="catalog_edit.php?ID="+tid;}),
    new ContextSeperator(),
    new ContextItem("移动栏目",function(){location='catalog_move.php?job=movelist&typeid='+tid}),
    new ContextItem("删除栏目",function(){location="catalog_del.php?ID="+tid+"&typeoldname="+tname;}),
    new ContextSeperator(),
    new ContextItem("向上排前一级",function(){ location="catalog_do.php?cid="+tid+"&dopost=upRank"; })
  ]
  ContextMenu.display(popupoptions)
}
//封面模板
function CommonMenuPart(obj,tid,tname)
{
  var eobj,popupoptions
  popupoptions = [
    new ContextItem("管理内容",function(){location="catalog_do.php?cid="+tid+"&dopost=listArchives";}),
    new ContextSeperator(),
    new ContextItem("预览分类",function(){ window.open("<?=$cfg_plus_dir?>/list.php?tid="+tid); }),
    new ContextItem("更新HTML",function(){ location="makehtml_list.php?cid="+tid; }),
    new ContextItem("获取JS文件",function(){ location="catalog_do.php?cid="+tid+"&dopost=GetJs"; }),
    new ContextSeperator(),
    new ContextItem("增加子类",function(){location="catalog_add.php?ID="+tid;}),
    new ContextItem("更改栏目",function(){location="catalog_edit.php?ID="+tid;}),
    new ContextSeperator(),
    new ContextItem("移动栏目",function(){location='catalog_move.php?job=movelist&typeid='+tid}),
    new ContextItem("删除栏目",function(){location="catalog_del.php?ID="+tid+"&typeoldname="+tname;}),
    new ContextSeperator(),
    new ContextItem("向上排前一级",function(){ location="catalog_do.php?cid="+tid+"&dopost=upRank"; })
  ]
  ContextMenu.display(popupoptions)
}
//单个页面
function SingleMenu(obj,tid,tname)
{
  var eobj,popupoptions
  popupoptions = [
    new ContextItem("预览页面",function(){ window.open("catalog_do.php?cid="+tid+"&dopost=viewSgPage"); }),
    new ContextItem("编辑页面",function(){ location="catalog_do.php?cid="+tid+"&dopost=editSgPage"; }),
    new ContextItem("编辑模板",function(){ location="catalog_do.php?cid="+tid+"&dopost=editSgTemplet"; }),
    new ContextSeperator(),
    new ContextItem("更改栏目",function(){location="catalog_edit.php?ID="+tid;}),
    new ContextSeperator(),
    new ContextItem("移动栏目",function(){location='catalog_move.php?job=movelist&typeid='+tid}),
    new ContextItem("删除栏目",function(){location="catalog_del.php?ID="+tid+"&typeoldname="+tname;}),
    new ContextSeperator(),
    new ContextItem("向上排前一级",function(){ window.location="catalog_do.php?cid="+tid+"&dopost=upRank"; })
  ]
  ContextMenu.display(popupoptions)
}
</script>
<style>
.coolbg2 {
border: 1px solid #000000;
background-color: #F2F5E9;
height:18px
}
.coolbt2 {
  border-left: 2px solid #EFEFEF;
  border-top: 2px solid #EFEFEF;
  border-right: 2px solid #ACACAC;
  border-bottom: 2px solid #ACACAC;
  background-color: #F7FCDA
}
.bline {border-bottom: 1px solid #BCBCBC;background-color:#F0F4F1;}
.nbline {border-bottom: 1px solid #DEDEDE;background-color:#FFFFFF;}
.bline2 {border-bottom: 1px solid #BCBCBC;background-color:#F8F8F8;}
</style>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8' onload="ContextMenu.intializeContextMenu()">
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
<tr>
    <td height="19" background='img/tbg.gif'><strong>网站栏目管理 </strong></td>
</tr>
<tr>
    <td height="19" bgcolor="#ffffff">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="51%">IE浏览器可使用右键菜单进行操作。</td>
          <td width="49%" align="right">
          	<a href='catalog_add.php?listtype=all'>[<u>增加顶级栏目</u>]</a> 
            <a href="makehtml_list.php">[<u>更新所有栏目HTML</u>]</a>
            <a href='catalog_do.php?dopost=viewTemplet'>[<u>管理通用模板</u>]</a>
          </td>
        </tr>
      </table>
   </td>
</tr>
<form name='form1' method='post' action='catalog_do.php?dopost=upRankAll'>
<tr>
<td height="120" bgcolor="#FFFFFF" valign="top">
<?
if(empty($opendir)) $opendir=-1;
if($userChannel>0) $opendir=$userChannel;
$tu = new TypeUnit();
$tu->ListAllType($userChannel,$opendir);
$tu->Close();
?>
<br/>
</td>
</tr>
</form>
<tr>
 <td height="36" bgcolor="#FFFFFF" align="center">
 <table width="98%" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <td align="right">
		  <input type="button" name="sb1" value="更新排序" style="width:70" class="coolbt" onclick="document.form1.submit();"> 
      <input type="button" name="sb4" value="获取JS" style="width:70" class="coolbt2" onclick="location='makehtml_js.php';">
		  <input type="button" name="sb2" value="更新栏目HTML" style="width:90" class="coolbt2" onclick="location='makehtml_list.php';"> 
      <input type="button" name="sb3" value="更新文档HTML" style="width:90" class="coolbt2" onclick="location='makehtml_archives.php';">
		  </td>
    </tr>
   </table>
 </td>
</tr>
</table>
</body>
</html>
<?
require_once("config.php");
require_once("inc_typelink.php");
if(empty($job)) $job="";
if(empty($artids)) $artids="";
if(!isset($ENV_GOBACK_URL)) $ENV_GOBACK_URL="list_news.php";
function delFile($ID,$typedir,$dtime,$rank)
{
	global $base_dir;
	$tl = new typeLink();
	$artfile = $base_dir.$tl->GetFileName($ID,$typedir,$dtime,$rank);
	if(file_exists($artfile)) unlink($artfile);
}
if($job=="ok")
{
     $conn = connectMySql();
     $artids = split("`",$artids);
     foreach($artids as $id)
     {
     	$id = trim($id);
     	if($id!="")
     	{
	 		$rs = mysql_query("Select dede_art.ID,dede_art.dtime,dede_art.rank,dede_arttype.typedir From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.ID=$id",$conn);
			$row=mysql_fetch_object($rs);
			delFile($id,$row->typedir,$row->dtime,$row->rank);
			mysql_query("Delete From dede_art where ID=$id",$conn);
		}
	 }
	 ShowMsg("成功删除指定文件！",$ENV_GOBACK_URL);
	 exit;
}
else
{
	if($artids=="")
	{
		ShowMsg("你没选中任何选项！",$ENV_GOBACK_URL);
		exit;
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>删除类目</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="450" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <tr> 
    <td height="19"  background='img/tbg.gif'>文章管理&nbsp;&nbsp;<del>删除文章</del> [<a href="list_news.php">文章管理</a>]</td>
  </tr>
  <tr> 
    <td height="95" align="center" bgcolor="#FFFFFF"> <table width="80%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1">
          <input type="hidden" name="artids" value="<?=$artids?>">
          <input type="hidden" name="job" value="ok">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">
            你要删除所选的文章？
            </td>
          </tr>
          <tr> 
            <td colspan="2" height="50">
            (删除文章不会改变原有文章的相互关连，需手工重新改变连并重新生成HTML)
            </td>
          </tr>
          <tr> 
            <td width="51%" height="30">
            <input type="button" name="Submit" value=" 确定 " onClick="javascript:document.form1.submit();"> 
              &nbsp; <input type="button" name="Submit2" value=" 返回 " onClick="javascript:location.href='<?=$ENV_GOBACK_URL?>';"></td>
          </tr>
          <tr> 
            <td height="20" colspan="2">&nbsp;</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>

</html>

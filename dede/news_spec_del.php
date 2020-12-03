<?
require("config.php");
$conn = connectMySql();
function delFile($ID,$typedir,$stime)
{
	global $base_dir;
	global $art_dir;
	global $art_shortname;
	global $art_nametag;
	list($y,$m,$d)=split("-",$stime);
	if($art_nametag=="listdir")
		$artfile = $base_dir.$art_dir."/".$y."/".$m.$d."/$ID".$art_shortname;
	else
		$artfile = $base_dir.$art_dir."/$typedir/$ID".$art_shortname;
	if(file_exists($artfile)) unlink($artfile);
}
if(isset($job))
{
	if($job=="yes")
	{

     	$artids = split("`",$ID);
     	foreach($artids as $id)
     	{
     		$id = trim($id);
     		if($id!="")
     		{
				$rs = mysql_query("Select dede_spec.AID,dede_spec.stime,dede_arttype.typedir from dede_spec left join dede_arttype on dede_arttype.ID=dede_spec.typeid where dede_spec.ID=$id",$conn);
				$row = mysql_fetch_object($rs);
				mysql_query("Delete From dede_art where ID=".$row->AID,$conn);
				delFile($row->AID,$row->typedir,$row->stime);
				mysql_query("Delete From dede_spec where ID=$id",$conn);
			}
		}
		echo "<script>alert('成功删除指定专题！');location.href='list_news_spec.php';</script>";
		exit();	
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>删除专题</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"> &nbsp;<strong>专题管理--删除专题</strong>&nbsp;&nbsp;<strong>[<a href="list_news_spec.php"><u>专题管理</u></a>]</strong></td>
</tr>
<tr>
    <td align="center" valign="top" bgcolor="#FFFFFF"> 
      <form name="form1">
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="job" value="yes">
	    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td height="6"></td>
          </tr>
          <tr> 
            <td height="30">
            你确实要删除如下所选择的专题吗？<br><br>
        <?
        $artids = split("`",$ID);
     	foreach($artids as $id)
     	{
     		$id = trim($id);
     		if($id!="")
     		{
				$rs = mysql_query("Select spectitle from dede_spec where ID=$id",$conn);
				$row = mysql_fetch_object($rs);
				echo "·".$row->spectitle."<br>\n";
			}
		}
          ?>
            <br>
            </td>
          </tr>
          <tr> 
            <td height="41"><input type="submit" name="Submit" value=" 确 认 "></td>
          </tr>
        </table>
	  </form>
	  </td>
</tr>
</table>
</body>
</html>
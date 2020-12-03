<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_plus');
$aid = ereg_replace("[^0-9]","",$aid);
if($dopost=="show")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__plus set isshow=1 where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功启用一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}
else if($dopost=="hide")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__plus set isshow=0 where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功禁用一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}
else if($dopost=="delete")
{
	if(empty($job)) $job="";
  if($job=="") //确认提示
  {
  	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
  	$wintitle = "删除插件";
	  $wecome_info = "<a href='plus_main.php'>插件管理</a>::删除插件";
	  $win = new OxWindow();
	  $win->Init("plus_edit.php","js/blank.js","POST");
	  $win->AddHidden("job","yes");
	  $win->AddHidden("dopost",$dopost);
	  $win->AddHidden("aid",$aid);
	  $win->AddTitle("你确实要删除'".$title."'这个插件？");
	  $winform = $win->GetWindow("ok");
	  $win->Display();
	  exit();
  }
  else if($job=="yes") //操作
  {
  	$dsql = new DedeSql(false);
	  $dsql->SetQuery("Delete From #@__plus where aid='$aid';");
	  $dsql->ExecuteNoneQuery();
	  $dsql->Close();
	  ShowMsg("成功删除一个插件,请刷新导航菜单!","plus_main.php");
	  exit();
  }
}
else if($dopost=="saveedit") //保存更改
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Update #@__plus set plusname='$plusname',menustring='$menustring',filelist='$filelist' where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改插件的配置!","plus_main.php");
  exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__plus where aid='$aid'");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>修改插件</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <form name="form1" action="plus_edit.php" method="post">
   <input type='hidden' name='dopost' value='saveedit'>
   <input type='hidden' name='aid' value='<?php echo $aid?>'>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="plus_main.php"><u>插件管理</u></a> 
        &gt; 修改插件：</b> </td>
    </tr>
    <tr> 
      <td width="19%" align="center" bgcolor="#FFFFFF">插件名称</td>
      <td width="81%" bgcolor="#FFFFFF">
      	<input type='text' name='plusname' style='width:180px' value='<?php echo $row['plusname']?>'>
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">作者</td>
      <td bgcolor="#FFFFFF">
      	<?php echo $row['writer']?>
      </td>
    </tr>
     <tr> 
      <td align="center" bgcolor="#FFFFFF">菜单配置</td>
      <td bgcolor="#FFFFFF">
      	<textarea name="menustring" rows="6" id="menustring" style="width:80%"><?php echo $row['menustring']?></textarea>
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">文件列表</td>
      <td bgcolor="#FFFFFF">文件用&quot;,&quot;分开，路径相对于管理目录（当前目录）<br>
        <textarea name="filelist" rows="8" id="filelist" style="width:80%"><?php echo $row['filelist']?></textarea></td>
    </tr>
    <tr bgcolor="#F9FDF0"> 
      <td height="28" colspan="2">
      	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%"><img src="img/button_back.gif" width="60" height="22" onClick="location='plus_main.php';" style="cursor:hand"></td>
          </tr>
        </table>
        </td>
    </tr>
  </form>
</table>
</body>
</html>
<?
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/../include/pub_dedetag.php");
if(empty($dopost)) $dopost="";
if(empty($aid)) $aid="";
$aid = trim(ereg_replace("[^0-9]","",$aid));
if($aid==""){
	ShowMsg('你没有指定投票ID！','-1');
	exit();
}
if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
else $ENV_GOBACK_URL = "vote_main.php";
///////////////////////////////////////
$dsql = new DedeSql(false);
if($dopost=="delete")
{
	$dsql->SetQuery("Delete From #@__vote where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg('成功删除一组投票!',$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="saveedit")
{
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "Update #@__vote set votename='$votename',
	starttime='$starttime',
	endtime='$endtime',
	totalcount='$totalcount',
	ismore='$ismore',
	votenote='$votenote' where aid='$aid'";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg('成功更改一组投票!',$ENV_GOBACK_URL);
	exit();
}
$row = $dsql->GetOne("Select * From #@__vote where aid='$aid'");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>投票管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><b>投票管理</b>&gt;&gt;增加投票&nbsp;&nbsp;[<a href="vote_main.php"><u>管理以往投票内容记录</u></a>]</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<form name="form1" method="post" action="vote_edit.php">
	<input type="hidden" name="dopost" value="saveedit">
	<input type="hidden" name="aid" value="<?=$aid?>">
	    <table width="100%" border="0" cellspacing="4" cellpadding="4">
          <tr> 
            <td width="15%" align="center">投票名称：</td>
            <td width="85%"> <input name="votename" type="text" id="votename" value="<?=$row['votename']?>"> 
            </td>
          </tr>
          <tr>
            <td align="center">投票总人数：</td>
            <td><input name="totalcount" type="text" id="totalcount" value="<?=$row['totalcount']?>"></td>
          </tr>
          <tr> 
            <td align="center">开始时间：</td>
            <td><input name="starttime" type="text" id="starttime" value="<?=GetDateMk($row['starttime'])?>"></td>
          </tr>
          <tr> 
            <td align="center">结束时间：</td>
            <td><input name="endtime" type="text" id="endtime" value="<?=GetDateMk($row['endtime'])?>"></td>
          </tr>
          <tr> 
            <td align="center">是否多选：</td>
            <td> <input name="ismore" type="radio" class="np" value="0"<?if($row['ismore']==0) echo " checked";?>>
              单选 　 
              <input type="radio" name="ismore" class="np" value="1"<?if($row['ismore']==1) echo " checked";?>>
              多选 </td>
          </tr>
          <tr> 
            <td align="center">投 票 项：<br/>
              (请按相同的形式来增加或修改节点，其中属性：id不能重复) </td>
            <td><textarea name="votenote" rows="8" id="votenote" style="width:80%"><?=$row['votenote']?></textarea> 
            </td>
          </tr>
          <tr> 
            <td height="47">&nbsp;</td>
            <td><input type="submit" name="Submit" value="保存投票数据"></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
	  </form>
	  </td>
</tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>
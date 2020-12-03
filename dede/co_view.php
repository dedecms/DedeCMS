<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
isset($_COOKIE['ENV_GOBACK_URL']) ? $backurl = $_COOKIE['ENV_GOBACK_URL'] : $backurl = "co_url.php";
if(empty($action)) $action="";
if($aid=="") {
	ShowMsg("参数无效!","-1");	
	exit();
}

//保存更改
if($action=="save"){
	$dsql = new DedeSql(false);
	$result = "";
	for($i=0;$i < $endid;$i++){
		$result .= "{dede:field name=\\'".${"noteid_$i"}."\\'}".${"value_$i"}."{/dede:field}\r\n";
	}
	$dsql->ExecuteNoneQuery("Update #@__courl set result='$result' where aid='$aid'; ");
	$dsql->Close();
	ShowMsg("成功保存一条记录！",$backurl);
	exit();
}

$dsql = new DedeSql(false);
$dsql->SetSql("Select * from #@__courl where aid='$aid'");
$dsql->Execute();
$row = $dsql->GetObject();
$isdown = $row->isdown;
$nid = $row->nid;
$url = $row->url;
$dtime = $row->dtime;
$body = $row->result;
$dsql->Close();
$fields = array();
if($isdown==0)
{
	$co = new DedeCollection();
	$co->Init();
	$co->LoadFromDB($nid);
	$co->DownUrl($aid,$url);
	$co->dsql->Init(false);
	$co->dsql->SetSql("Select * from #@__courl where aid='$aid'");
	$co->dsql->Execute();
	$row = $co->dsql->GetObject();
	$isdown = $row->isdown;
	$nid = $row->nid;
	$url = $row->url;
	$dtime = $row->dtime;
	$body = $row->result;
	$co->Close();
}
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadString($body);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>采集内容预览</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
<form name='form1' action='co_view.php?action=save&aid=<?php echo $aid?>' method='post'>
  <tr> 
    <td height="28" colspan="2" background='img/tbg.gif'>
    	<b><a href='co_url.php'>采集内容管理</a> &gt; 采集内容预览：</b>(系统会自动下载并处理未下载过的内容)
   </td>
  </tr>
  <tr bgcolor="#F8FCF1"> 
    <td width="17%" height="24" align="center" bgcolor="#F8FCF1"><strong>字段名称</strong></td>
    <td width="83%" align="center" bgcolor="#F8FCF1"><strong>内　容</strong></td>
  </tr>
 <?php 
for($i=0;$i<=$dtp->Count;$i++)
{
	$ctag = $dtp->CTags[$i];
	if($ctag->GetName()=="field")
	{
		$fname = $ctag->GetAtt("name");
		$fvalue = $ctag->GetInnerText();
 ?>
  <tr> 
  <td height="24" align="center" valign="top" bgcolor="#FFFFFF">
	<?php echo $fname?>
	<input type='hidden' name='noteid_<?php echo $i?>' value='<?php echo $fname?>'>
	</td>
    <td bgcolor="#FFFFFF">
	<?php 
	if(strlen($fvalue)<200) echo "<textarea name=\"value_$i\" rows=\"1\" style=\"width:90%\">$fvalue</textarea>";
	else echo "<textarea name=\"value_$i\" rows=\"12\" style=\"width:90%\">$fvalue</textarea>";
	?>
	</td>
  </tr>
<?php 
  }
}
$dtp->Clear();
?>
<input type='hidden' name='endid' value='<?php echo $i?>'>
  <tr bgcolor="#F8FCF1"> 
    <td height="24" colspan="2" align="center">
    	<input name="imageField" type="image" src="img/button_save.gif" class="np" width="60" height="22" border="0">
    	&nbsp;
     <a href="<?php echo $backurl?>"><img src="img/button_back.gif" class="np" width="60" height="22" border="0"></a>
    </td>
  </tr>
</table>
</form>
</body>
</html>

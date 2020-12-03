<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_投票模块');
if(empty($dopost)) $dopost = "";
//////////////////////////////////////////
if($dopost=="save")
{
	//$ismore,$votename
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$voteitems = "";
	$j=0;
	for($i=1;$i<=15;$i++)
	{
		if(!empty(${"voteitem".$i})){
			$j++;
			$voteitems .= "<v:note id=\\'$j\\' count=\\'0\\'>".${"voteitem".$i}."</v:note>\r\n";
		}
	}
	$dsql = new DedeSql(false);
	$inQuery = "
	insert into #@__vote(votename,starttime,endtime,totalcount,ismore,votenote) 
	Values('$votename','$starttime','$endtime','0','$ismore','$voteitems');
	";
	$dsql->SetQuery($inQuery);
	if(!$dsql->ExecuteNoneQuery())
	{
		$dsql->Close();
		ShowMsg("增加投票失败，请检查数据是否非法！","-1");
		exit();
	}
	$dsql->Close();
	ShowMsg("成功增加一组投票！","vote_main.php");
	exit();
}
$startDay = mytime();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>增加投票</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
var i=1;
function AddItem()
{ 
  i++;
  if(i>15){
		alert("最大只允许15个选项！");
		return;
  }
  var obj = document.getElementById("voteitem");
  obj.innerHTML+="<br/>选项"+i+"： <input name='voteitem"+i+"' type='text' size='30'>";
}
function ResetItem()
{ 
  i = 1;
	var obj = document.getElementById("voteitem");
	obj.innerHTML="选项1： <input name='voteitem1' type='text' size='30'>";
}
function checkSubmit()
{
	if(document.form1.votename.value=="")
	{
		alert("投票名称不能为空！");
		document.form1.votename.focus();
		return false;
	}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <tr>
    <td height="19" background="img/tbg.gif"><b>投票管理</b>&gt;&gt;增加投票&nbsp;&nbsp;[<a href="vote_main.php"><u>管理以往投票内容记录</u></a>]</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="4">
        <form name="form1" method="post" action="vote_add.php" onSubmit="return checkSubmit()">
		<input type='hidden' name='dopost' value='save'>
		<tr> 
          <td width="15%" align="center">投票名称：</td>
          <td width="85%"> <input name="votename" type="text" id="votename"> </td>
        </tr>
        <tr> 
          <td align="center">开始时间：</td>
          <td><input name="starttime" type="text" id="starttime" value="<?php echo $startDay?>"></td>
        </tr>
        <tr> 
          <td align="center">结束时间：</td>
          <td><input name="endtime" type="text" id="endtime" value="<?php echo $endDay?>"></td>
        </tr>
        <tr> 
          <td align="center">是否多选：</td>
          <td> <input name="ismore" type="radio" class="np" value="0" checked>
            单选 　 
            <input type="radio" name="ismore" class="np" value="1">
            多选 </td>
        </tr>
        <tr>
          <td align="center">投 票 项：</td>
          <td>
          	<input type="button" value="增加投票选项" name="bbb"  onClick="AddItem();" class='nbt'>
            　 
            <input type="button" value="重置投票选项" name="bbb2" onClick="ResetItem();" class='nbt'>
          </td>
        </tr>
        <tr> 
          <td></td>
          <td>
		  <div id="voteitem">
			选项1： 
                <input name="voteitem1" type="text" id="voteitem1" size="30">
		  </div>
		  </td>
        </tr>
        <tr> 
          <td height="47">&nbsp;</td>
          <td><input type="submit" name="Submit" value="保存投票数据"></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
		</form>
      </table>
	 </td>
</tr>
</table>
</body>
</html>
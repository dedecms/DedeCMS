<?
require_once("config.php");
require_once("inc_vote.php");
$votetable="";
$votecode="";
if(!empty($votename))
{
	$vo = new DedeVote();
	$vo->SetVote($votename);
	if(!empty($job)) 
	{
		if($job=="del") 
		{
			$vo->DelVote();
			echo "<script language='javascript'>\r\n";
			echo "alert('成功删除一组投票！');";
			echo "location.href='add_vote.php';";
			echo "</script>";
			exit();
		}
	}
	$votetable =  $vo->GetVoteForm();
	$votecode = "{dede:vote name='$votename'/}";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>成功提示</title>
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
td {font-size: 9pt;line-height: 1.5;}
body {font-size: 9pt;line-height: 1.5;}
a:link { font-size: 9pt; color: #000000; text-decoration: none }
a:visited{ font-size: 9pt; color: #000000; text-decoration: none }
a:hover {color: red;background-color:yellow}
</style>
</head>
<body background="img/allbg.gif" leftmargin="6" topmargin="6">
<table width="90%" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666" align="center">
  <tr align="center"> 
    <td height="26" colspan="2" background='img/tbg.gif'><strong>投票数据代码管理</strong></td>
  </tr>
          <tr bgcolor="#FFFFFF"> 
          
          
    <td height="85" colspan="2" align="center"> <table width="100%" border="0" cellspacing="4" cellpadding="4">
        <tr> 
          <td width="22%" height="44">板块代码：</td>
          <td width="78%"><textarea name="ta1" cols="60" rows="2" id="ta1"><?=$votecode?></textarea></td>
        </tr>
        <tr>
          <td height="59">投票表单：</td>
          <td><table width="180" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td><?=$votetable?></td>
            </tr>
          </table></td>
        </tr>
        <tr> 
          <td height="59">表单HTML：</td>
          <td><textarea name="ta2" cols="60" rows="5" id="ta2"><?=$votetable?></textarea></td>
        </tr>
        <tr align="center" bgcolor="#E8FAE7"> 
          <td height="24" colspan="2"><a href="add_vote.php"><u>&lt;&lt;管理投票&gt;&gt;</u></a>　<a href="add_vote_new.php"><u>&lt;&lt;新增投票&gt;&gt;</u></a></td>
        </tr>
      </table></td>
          </tr>
</table>
</body>
</html>
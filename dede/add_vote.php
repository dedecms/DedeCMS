<?
require_once("config.php");
require_once("inc_vote.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>投票管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
function DelVote(gurl)
{
	if(window.confirm('你确定要删除这则投票吗?')) location.href="get_vote.php?votename="+gurl+"&job=del";
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><b>投票管理　[<a href="add_vote_new.php"><u>新增一组投票</u></a>]</b></td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top"><table width="98%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFF4EC">
        <tr align="center" bgcolor="#D5E3B0"> 
          <td width="44%">投票名称</td>
          <td width="24%">创建日期</td>
          <td width="32%">内容管理</td>
        </tr>
		<?
		$votepath = $base_dir.$art_php_dir."/vote";
		$dh = dir($votepath);
		$vo = new DedeVote();
		while($filename=$dh->read())
		{
		if(ereg("dat",$filename))
		{
		$filename = ereg_replace("\.dat$","",$filename);
		$vo->SetVote($filename);
		$ufilename = urlencode($filename);
		?>
        <tr bgcolor="#FFFFFF"> 
          <td> &nbsp;<?=$filename?></td>
          <td align="center"><?=$vo->GetMakeTime();?></td>
          <td align="center">[<a href="get_vote.php?votename=<?=$ufilename?>">获取代码</a>]
		   
		    [<a href="<?=$art_php_dir."/"?>vote.php?job=view&id=<?=$ufilename?>" target="_blank">查看结果</a>]
			[<a href="javascript:DelVote('<?=$filename?>');">删除</a>] </td>
        </tr>
		<?
		}
		}
		?>
      </table> </td>
</tr>
</table>
</body>
</html>
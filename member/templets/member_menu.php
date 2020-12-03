<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
<tr align="center"> 
<td height="26" colspan="2" background="img/mmbg.gif" class="mmt1m">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
<tr> 
<td width="28%" height="24" align="right" valign="top" style="padding-top:3px"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
<td width="72%"><strong>个人基本信息</strong></td>
</tr>
</table>
</td>
</tr>
<tr bgcolor="#FFFFFF"> 
<td height="50" colspan="2" align="center" bgcolor="#FFFFFF" class="mma"> 
<table width="98%" border="0" cellspacing="2" cellpadding="2">
<tr> 
<td align="center"> <a href="index.php?uid=<?=$uid?>"><img name="myface" src="<?=$spaceimage?>" width="150" height="110" border="0"></a> 
</td>
</tr>
<tr> 
<td align="center" bgcolor="#F8FEE0" class="mmbb"> 昵称： 
<?=$uname?>
&nbsp; <a href="index.php?uid=<?=$uid?>&action=memberinfo">档案</a> 
</td>
</tr>
<tr> 
<td align="center" bgcolor="#F8FEE0" class="mmbb"> 最后在线： 
<?=strftime("%y-%m-%d %H:%M",$logintime)?>
</td>
</tr>
<tr> 
<td align="center" bgcolor="#F8FEE0" class="mmbb">
文章[<a href="member_archives.php?uid=<?=$uid?>&channelid=1"><?= $c1?></a>] 
图集[<a href="member_archives.php?uid=<?=$uid?>&channelid=2"><?= $c2?></a>]
</td>
</tr>
</table>
 </td>
</tr>
<tr> 
<td colspan="2" height="6" class="mmb">&nbsp;</td>
</tr>
</table>
<?
if(empty($notarchives)){
?>
<!-- //文章 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
<tr align="center"> 
<td height="22" colspan="2" class="mmt">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
<tr> 
<td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
<td width="72%"><strong>文档分类</strong></td>
</tr>
</table>
</td>
</tr>
<tr bgcolor="#FFFFFF"> 
<td colspan="2" align="center" class="mma">
<table width="98%" border="0" cellspacing="0" cellpadding="0">
<?
if(!isset($dsql) || !is_object($dsql)){
	$dsql = new DedeSql(false);
}
$addQuery = "";
if(!empty($channelid)){
	$channelid = ereg_replace("[^0-9]","",$channelid);
	$addQuery = " And channelid='$channelid' ";
}
$userNumID = ereg_replace("[^0-9]","",$userNumID);
$dsql->SetQuery("Select * From #@__member_arctype where memberid='$userNumID' $addQuery order by rank desc; ");
$dsql->Execute();
while($menurow = $dsql->GetArray())
{
	if($menurow['channelid']==1) $tf = '<img src="img/dd/exe.gif" width="16" height="16">';
	else $tf='<img src="img/dd/image.gif" width="16" height="16">';
?>
 <tr> 
<td align="right" class="mmbb" height="24" width="30%" style="padding-right:6px"><?=$tf?></td>
 <td class="mmbb">
 <a href='member_archives.php?uid=<?=$uid?>&channelid=<?=$menurow['channelid']?>&mtype=<?=$menurow['aid']?>'><?=$menurow['typename']?></a>
 </td>
 </tr>
<?
}
?>
</table>
</td>
</tr>
<tr> 
<td colspan="2" height="6" class="mmb">&nbsp;</td>
</tr>
</table>
<?
}else{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
<tr align="center"> 
<td height="22" colspan="2" class="mmt1">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
<tr> 
<td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
<td width="72%"><strong>文档分类</strong></td>
</tr>
</table>
 </td>
</tr>
<tr bgcolor="#FFFFFF"> 
<td colspan="2" align="center" class="mma">
<table width="98%" border="0" cellspacing="0" cellpadding="0">
<tr> 
 <td align="right" class="mmbb" height="24" width="30%" style="padding-right:6px">
 	<img src="img/dd/exe.gif" width="16" height="16">
 	</td>
 <td class="mmbb">
 <a href='member_archives.php?uid=<?=$uid?>&channelid=1'>我的文章</a>
 </td>
</tr>
<? if($cfg_mb_album=='是'){ ?>
<tr> 
 <td align="right" class="mmbb" height="24" width="30%" style="padding-right:6px">
 	<img src="img/dd/image.gif" width="16" height="16">
 </td>
 <td class="mmbb">
 <a href='member_archives.php?uid=<?=$uid?>&channelid=2'>我的图集</a>
 </td>
</tr>
<? } ?>
</table>
</td>
</tr>
<tr> 
<td colspan="2" height="6" class="mmb">&nbsp;</td>
</tr>
</table>
<?
}
?>

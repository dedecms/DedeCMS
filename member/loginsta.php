<?
header("Pragma:no-cache\r\n");
header("Cache-Control:no-cache\r\n");
header("Expires:0\r\n");
header("Content-Type: text/html; charset=gb2312");
require_once(dirname(__FILE__)."/config_space.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
$cfg_ml = new MemberLogin(); 
if(empty($cfg_ml->M_ID)){ echo ""; exit(); }
$uid = $cfg_ml->M_LoginID;
$dsql = new DedeSql(false);
$spaceInfos = $dsql->GetOne("Select ID,uname,spacename,spaceimage,sex,c1,c2,spaceshow,logintime,news From #@__member where userid='$uid'; ");
if(!is_array($spaceInfos)){
	$dsql->Close(); echo ""; exit();
}
$dsql->Close();
foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
if($spaceimage==''){
	if($sex=='女') $spaceimage = $cfg_memberurl.'/img/dfgril.gif';
	else $spaceimage = $cfg_memberurl.'/img/dfboy.gif';
}
?>
<table width="212" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="27" colspan="2" align="center" style="border-bottom:1px solid $cdcdcd;background-color:#FAFAFA"><img src="<?=$cfg_templeturl?>/img/menumember.gif" width="16" height="15">你好：<font color='#2D78EA'>
      <?=$cfg_ml->M_UserName?>
      </font>，欢迎登录</td>
</tr>
<tr> 
<td rowspan="3" align="center">
	<img name="ok" src="<?=$spaceimage?>" width="100" height="75" alt="<?=$spacename?>" border="0"> 
</td>
<td width="40%" height="29" align="center"><a href="<?=$cfg_memberurl?>/guestbook_admin.php" class="mbline">[我的留言]</a></td>
</tr>
<tr> 
<td height="29" align="center"><a href="<?=$cfg_memberurl?>/mystow.php" class="mbline">[我的收藏]</a></td>
</tr>
<tr> 
<td height="29" align="center"><a href="<?=$cfg_memberurl?>/article_add.php" class="mbline">[发表文章]</a></td>
</tr>
<tr align="center"> 
    <td height="30" colspan="2"> <table width="96%" height="26" border="0" cellpadding="1" cellspacing="1" bgcolor="#99ACB6">
        <tr>
          <td align="center" bgcolor="#DBEAF2"><a href="<?=$cfg_memberurl?>/index.php?uid=<?=$uid?>">我的空间</a> 
            | <a href="<?=$cfg_memberurl?>/index.php">控制面板</a> | <a href="<?=$cfg_memberurl?>/index_do.php?fmdo=login&dopost=exit">退出系统</a></td>
        </tr>
      </table></td>
</tr>
</table>
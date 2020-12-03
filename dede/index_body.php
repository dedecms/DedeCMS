<?php
require(dirname(__FILE__).'/config.php');
require(DEDEINC.'/image.func.php');
require(DEDEINC.'/dedetag.class.php');

$defaultIcoFile = DEDEROOT.'/data/admin/quickmenu.txt';
$myIcoFile = DEDEROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
if(!file_exists($myIcoFile)) {
	$myIcoFile = $defaultIcoFile;
}
if(empty($dopost)) {
	$dopost = '';
}

/*------------
增加新项
function _AddNew() {   }
-------------*/
if($dopost=='addnew')
{
	if(empty($link) || empty($title))
	{
		ShowMsg("链接网址或标题不能为空！","-1");
		exit();
	}

	$fp = fopen($myIcoFile,'r');
	$oldct = trim(fread($fp,filesize($myIcoFile)));
	fclose($fp);

	$link = ereg_replace("['\"]",'`',$link);
	$title = ereg_replace("['\"]",'`',$title);
	$ico = ereg_replace("['\"]",'`',$ico);
	$oldct .= "\r\n<menu:item ico=\"{$ico}\" link=\"{$link}\" title=\"{$title}\" />";

	$myIcoFileTrue = DEDEROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
	$fp = fopen($myIcoFileTrue,'w');
	fwrite($fp,$oldct);
	fclose($fp);

	ShowMsg("成功增加一个项目！","index_body.php?".time());
	exit();
}
/*------------
保存修改的项
function _EditSave() {   }
-------------*/
else if($dopost=='editsave')
{
	$quickmenu = stripslashes($quickmenu);

	$myIcoFileTrue = DEDEROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
	$fp = fopen($myIcoFileTrue,'w');
	fwrite($fp,$quickmenu);
	fclose($fp);

	ShowMsg("成功修改快捷操作项目！","index_body.php?".time());
	exit();
}
/*------------
显示修改表单
function _EditShow() {   }
-------------*/
else if($dopost=='editshow')
{
	$fp = fopen($myIcoFile,'r');
	$oldct = trim(fread($fp,filesize($myIcoFile)));
	fclose($fp);
?>
<form name='editform' action='index_body.php' method='post'>
<input type='hidden' name='dopost' value='editsave' />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
     <td height='28' background="img/tbg.gif">
     	<div style='float:left'><b>修改快捷操作项</b></div>
      <div style='float:right;padding:3px 10px 0 0;'>
     		<a href="javascript:CloseTab('editTab')"><img src="img/close.gif" width="12" height="12" border="0" /></a>
      </div>
     </td>
   </tr>
 	 <tr><td style="height:6px;font-size:1px;border-top:1px solid #8DA659">&nbsp;</td></tr>
   <tr>
     <td>
     	按原格式修改/增加XML项。
     </td>
   </tr>
   <tr>
     <td align='center'>
     	<textarea name="quickmenu" rows="10" cols="50" style="width:98%;height:220px"><?php echo $oldct; ?></textarea>
     </td>
   </tr>
   <tr>
     <td height="45" align="center">
     	<input type="submit" name="Submit" value="保存项目" class="np coolbg" style="width:80px;cursor:pointer" />
     	&nbsp;
     	<input type="reset" name="reset" value="重设" class="np coolbg" style="width:50px;cursor:pointer" />
     </td>
   </tr>
  </table>
</form>
<?php
exit();
}
else
{
	require(DEDEINC.'/inc/inc_fun_funAdmin.php');
	$verLockFile = DEDEROOT.'/data/admin/ver.txt';
	$fp = fopen($verLockFile,'r');
	$upTime = trim(fread($fp,64));
	fclose($fp);
	$oktime = substr($upTime,0,4).'-'.substr($upTime,4,2).'-'.substr($upTime,6,2);
	$offUrl = SpGetNewInfo();
	$dedecmsidc = DEDEROOT.'/data/admin/idc.txt';
	$fp = fopen($dedecmsidc,'r');
	$dedeIDC = fread($fp,filesize($dedecmsidc));
	fclose($fp);
	include DedeInclude('templets/index_body.htm');
	exit();
}
?>
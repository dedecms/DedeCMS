<?php 
require_once("config.php");
CheckPurview('t_Move');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($typeid)) $typeid="";
if(empty($job)) $job="movelist";
$typeid = ereg_replace("[^0-9]","",$typeid);
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select reID,typename,channeltype From #@__arctype where ID='$typeid'");
$typename = $row['typename'];
$reID = $row['reID'];
$channelid = $row['channeltype'];
//移动栏目
//------------------
if($job=="moveok")
{
	if($typeid==$movetype)
	{
		$dsql->Close();
		ShowMsg("移对对象和目标位置相同！","catalog_main.php");
	  exit();
	}
	if(IsParent($movetype,$typeid,$dsql))
	{
		$dsql->Close();
		ShowMsg("不能从父类移动到子类！","catalog_main.php");
	  exit();
	}
	$dsql->SetQuery("Update #@__arctype set reID='$movetype' where ID='$typeid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	//更新树形菜单
   $rndtime = time();
   $rflwft = "<script language='javascript'>
   if(window.navigator.userAgent.indexOf('MSIE')>=1){
     if(top.document.frames.menu.location.href.indexOf('catalog_menu.php')>=1)
     { top.document.frames.menu.location = 'catalog_menu.php?$rndtime'; }
   }else{
  	 if(top.document.getElementById('menu').src.indexOf('catalog_menu.php')>=1)
     { top.document.getElementById('menu').src = 'catalog_menu.php?$rndtime'; }
   }
   </script>";
   echo $rflwft;
	ShowMsg("成功移动目录！","catalog_main.php");
	exit();
}
function IsParent($myid,$topid,$dsql)
{
	$row = $dsql->GetOne("select ID,reID from #@__arctype where ID='$myid'");
	if($row['reID']==$topid) return true;
	else if($row['reID']==0) return false;
	else return IsParent($row['reID'],$topid,$dsql);
}
///////////////////////////////////////////////////


$tl = new TypeLink($typeid);
$typeOptions = $tl->GetOptionArray(0,0,$channelid);
$tl->Close();
$dsql->Close();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>移动列表</title>
<style type="text/css">
body {background-image: url(img/allbg.gif);}
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#98CAEF">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif">
    &nbsp;<a href="catalog_main.php"><u>栏目管理</u></a>&gt;&gt;移动列表
    </td>
  </tr>
  <tr>
    <td height="200" colspan="2" valign="top" bgcolor="#FFFFFF"> 
      <form name="form1" method="get" action="catalog_move.php">
      <input name="typeid" type="hidden" id="typeid" value="<?php echo $typeid?>">
      <input name="job" type="hidden" id="job" value="moveok">
	    <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td colspan="2" height="12"></td>
          </tr>
          <tr> 
            <td height="25" colspan="2" bgcolor="#F2F8FB">
            &nbsp;移动目录时不会删除原来已创建的列表，移动后需重新对栏目创建HTML。 
            </td>
          </tr>
          <tr> 
            <td width="30%" height="25">你选择的栏目是：</td>
            <td width="70%">
            <?php 
			echo "$typename($typeid)";
            ?>
            </td>
          </tr>
          <tr> 
            <td height="30">你希望移动到那个栏目？</td>
            <td>
            <select name="movetype">
              <option value='0'>移动为顶级栏目</option>
              <?php echo $typeOptions?>
             </select>
            </td>
          </tr>
          <tr> 
            <td height="25" colspan="2" bgcolor="#F2F8FB">
            &nbsp;不允许从父级移动到子级目录，只允许子级到更高级或同级或不同父级的情况。
             </td>
          </tr>
          <tr> 
            <td height="74">&nbsp;</td>
            <td>
            <input type="submit" name="Submit" value="确定操作" class='nbt'> 　 
            <input name="Submit11" type="button" id="Submit11" value="-不理返回-" onClick="history.go(-1);" class='nbt'>
            </td>
          </tr>
        </table>
	  </form>
	  </td>
  </tr>
</table>
</body>
</html>
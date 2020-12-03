<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
$ID = trim(ereg_replace("[^0-9]","",$ID));

//检查权限许可
CheckPurview('t_Del,t_AccDel');
//检查栏目操作许可
CheckCatalog($ID,"你无权删除本栏目！");

if(empty($dopost)) $dopost="";
if($dopost=="ok"){
	 $ut = new TypeUnit();
	 $ut->DelType($ID,$delfile);
	 //更新缓存
   UpDateCatCache($dsql);
	 $ut->Close();
	 //更新树形菜单
   $rndtime = time();
   $uptree = "<script language='javascript'>
   if(window.navigator.userAgent.indexOf('MSIE')>=1){
     if(top.document.frames.menu.location.href.indexOf('catalog_menu.php')>=1)
     { top.document.frames.menu.location = 'catalog_menu.php?$rndtime'; }
   }else{
  	 if(top.document.getElementById('menu').src.indexOf('catalog_menu.php')>=1)
     { top.document.getElementById('menu').src = 'catalog_menu.php?$rndtime'; }
   }
   </script>";
   echo $uptree;
	 ShowMsg("成功删除一个栏目！","catalog_main.php");
	 exit();
}

require_once(dirname(__FILE__)."/templets/catalog_del.htm");

ClearAllLink();
?>
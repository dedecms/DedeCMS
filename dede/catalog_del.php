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
	 $ut->Close();
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
	 ShowMsg("成功删除一个栏目！","catalog_main.php");
	 exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>删除栏目</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <tr> 
    <td height="19" background='img/tbg.gif'><a href="catalog_main.php"><u>栏目管理</u></a>&gt;&gt;删除栏目</td>
  </tr>
  <tr> 
    <td height="60" align="center" bgcolor="#FFFFFF"> 
      <table width="96%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" action="catalog_del.php" method="post">
          <input type="hidden" name="ID" value="<?php echo $ID?>">
          <input type="hidden" name="dopost" value="ok">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">你要删除栏目： 
              <?php echo $typeoldname?>
            </td>
          </tr>
          <tr> 
            <td colspan="2">栏目的文件保存目录： 
              <?php 
              $dsql = new DedeSql();
              $dsql->SetQuery("Select typedir From #@__arctype where ID=".$ID);
              $row = $dsql->GetOne();
              $dsql->Close();
              echo $row["typedir"];
              ?>
            </td>
          </tr>
          <tr> 
            <td width="42%" height="36">是否删除文件： 
              <input type="radio" name="delfile" class="np" value="no" checked>
              否 &nbsp;&nbsp; <input type="radio" name="delfile" class="np" value="yes">
              是 </td>
            <td width="58%" height="36"><input type="button" name="Submit" value=" 确定 " onClick="javascript:document.form1.submit();" class='nbt'> 
              &nbsp; <input type="button" name="Submit2" value=" 返回 " onClick="javascript:location.href='catalog_main.php';" class='nbt'> 
            </td>
          </tr>
          <tr> 
            <td height="20" colspan="2">&nbsp;</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>

</html>

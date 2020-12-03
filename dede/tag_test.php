<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select * From #@__homepageset");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>主页更新向导</title>
<link href="base.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="../include/calendar/calendar-win2k-1.css" title="win2k-1" />
<script type="text/javascript" src="../include/calendar/calendar.js"></script>
<script type="text/javascript" src="../include/calendar/calendar-cn.js"></script>
<script language="javascript">
function SelectTemplets(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/select_templets.php?f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="tag_test_action.php" target="stafrm" method="post">
    <input type="hidden" name="dopost" value="make">
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>全局标记测试：</strong></td>
            <td width="70%" align="right">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" bgcolor="#FFFFFF">
      	　　全局标记指的是应用在网站主页、单独页面、频道封面使用的单独的模板标记，在列表或文章模板中，一般只允许调用channel、arclist标记（hotart、coolart、imglist等都是这个标记延伸出来的标记），但是环境变量限定为文章或列表所在的栏目，如果你要测试的标记是在列表或文章中使用，请指定环境变量（栏目ＩＤ）。<br/>
        　　各标记的具体含义和用途，请在<a href="help_templet.php" target="_blank"><u>模板标记参考</u></a>一章查阅。
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" bgcolor="#FFFFFF">输入要测试的局部代码： </td>
    </tr>
    <tr> 
      <td height="62" colspan="2" bgcolor="#FFFFFF">
	  <textarea name="partcode" id="partcode" style="width:100%;height:120"></textarea>
	  </td>
    </tr>
    <tr> 
      <td width="103" height="20" valign="top" bgcolor="#FFFFFF">环境变量：</td>
      <td width="865" height="20" valign="top" bgcolor="#FFFFFF">&nbsp;
      	<?
       if(empty($cid)) $cid="0";
       $tl = new TypeLink($cid);
       $typeOptions = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),0);
       echo "<select name='typeid' style='width:300'>\r\n";
       if($cid=="0") echo "<option value='0' selected>不使用环境ID...</option>\r\n";
       echo $typeOptions;
       echo "</select>";
			 $tl->Close();
		  ?>
      </td>
    </tr>
    <tr> 
      <td height="31" colspan="2" bgcolor="#FAFAF1" align="center">
      	<input type="submit" name="Submit" value="提交测试"> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E6F3CD"> 
    <td height="20" colspan="2"><table width="100%">
        <tr> 
          <td width="74%">进行状态： </td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight + 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="2" id="mtd">
    	<div id='mdv' style='width:100%;height:300;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
    </td>
  </tr>
</table>
</body>
</html>

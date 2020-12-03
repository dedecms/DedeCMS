<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_New');
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ID From #@__channeltype order by ID desc limit 0,1 ");
$newid = $row['ID']+1;
if($newid<10) $newid = $newid+10;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>新增频道</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<script language="javascript">
<!--
function SelectGuide(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-200;
   window.open("mychannel_field_make.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=420,left="+posLeft+", top="+posTop);
}
-->
</script>
<link href="base.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.STYLE1 {color: #FF0000}
-->
</style>
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <form name="form1" action="mychannel_add_action.php" method="post">
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> 
        &gt; 新增频道模型：</b> </td>
    </tr>
    <tr> 
      <td width="19%" height="28" align="center" bgcolor="#FFFFFF">频道ID</td>
      <td width="81%" bgcolor="#FFFFFF"> <input name="ID" type="text" id="ID" size="10" value="<?php echo $newid?>">
        * （数字，不可更改，并具有唯一性） </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">名字标识</td>
      <td bgcolor="#FFFFFF"> <input name="nid" type="text" id="nid">
        *<br> 
        （不可更改，并具有唯一性，建议由英文、数字或下划线组成，因为部份Unix系统无法识别中文文件，频道默认文档模板是 “default/article_名字标识.htm”，列表模板、封面模板类推） </td>
    </tr>
    
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">频道名称</td>
      <td bgcolor="#FFFFFF"> <input name="typename" type="text" id="typename">
        * （频道的中文名称） </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">附加表</td>
      <td bgcolor="#FFFFFF"> <input name="addtable" type="text" id="addtable" value="<?php echo $cfg_dbprefix; ?>addon<?php echo $newid; ?>">
        必须由英文、数字、下划线组成 * 
        <input name="ismake" type="checkbox" class="np" id="ismake" value="1">
        我已经手动创建了表 </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">模型性质</td>
      <td bgcolor="#FFFFFF"> <input name="issystem" type="radio" class="np" value="0" checked>
        自动模型 
        <input type="radio" name="issystem" value="1" class="np">
        系统模型　（如果为<u>系统模型</u>将禁止删除，此选项不可更改） </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">是否支持会员投稿：</td>
      <td bgcolor="#FFFFFF"> <input name="issend" type="radio" class="np" value="0" checked>
        不支持　 
        <input type="radio" name="issend" class="np" value="1">
        支持 </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">会员许可投稿级别：</td>
      <td bgcolor="#FFFFFF"><select name="sendrank" id="sendrank" style="width:120">
          <?php 
              $urank = $cuserLogin->getUserRank();
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank' And rank>=10");
              $dsql->Execute();
              while($row2 = $dsql->GetObject())
              {
              	echo "     <option value='".$row2->rank."'>".$row2->membername."</option>\r\n";
              }
          ?>
        </select> </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">会员稿件默认状态：</td>
      <td bgcolor="#FFFFFF"><input name="arcsta" class="np" type="radio" value="-1" checked>
        未审核 
        <input name="arcsta" class="np" type="radio" value="0">
        已审核（自动生成HTML） 
        <input name="arcsta" class="np" type="radio" value="1">
        已审核（仅使用动态文档）</td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">列表附加字段：</td>
      <td bgcolor="#FFFFFF"><input name="listadd" type="text" id="listadd" size="50">
        <br>
(用&quot;,&quot;分开，可以在列表模板{dede:list}{/dede:list}中用[field:name/]调用) </td>
    </tr>
    <tr>
      <td height="45" align="center" bgcolor="#FFFFFF">附加字段配置：</td>
      <td bgcolor="#FFFFFF"><span class="STYLE1">当前版本或更高版本的Dedecms中，不需要在建立模型时设置字段，建立模型后在“更改”模型的地方添加字段即可。</span></td>
    </tr>
    
    <tr>
      <td colspan="2" bgcolor="#F9FFEC">　　◎以下的选项，如果你没有自己重新开发这个模型的程序，保留默认即可，系统会自动生成发布表单。</td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">档案发布程序</td>
      <td bgcolor="#FFFFFF"> <input name="addcon" type="text" id="addcon" value="archives_add.php" size="35">
        * </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">档案修改程序</td>
      <td bgcolor="#FFFFFF"> <input name="editcon" type="text" id="editcon" value="archives_edit.php" size="35">
        * </td>
    </tr>
    <tr> 
      <td height="28" align="center" bgcolor="#FFFFFF">档案管理程序</td>
      <td bgcolor="#FFFFFF"><input name="mancon" type="text" id="mancon" value="content_list.php" size="35">        
      * </td>
    </tr>
    
    <tr bgcolor="#F9FDF0"> 
      <td height="28" colspan="2"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%" height="45">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%"><img src="img/button_back.gif" width="60" height="22" onClick="location='mychannel_main.php';" style="cursor:hand"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>
<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
CheckPurview('c_Edit');
$ID = ereg_replace("[^0-9\-]","",$ID);
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改频道字段</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<script language="javascript">
<!--
function SelectGuide(fname,chid)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-200;
   window.open("mychannel_field_make.php?chid="+chid+"&f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=420,left="+posLeft+", top="+posTop);
}
//删除
function DelNote(gourl){
	if(!window.confirm("你确认要删除这条记录么！")){ return false; }
	location.href=gourl;
}
-->
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#98CAEF">
   <tr bgcolor="#F0F9D7">
     <td colspan="4" background="img/tbg.gif"><table width="98%" border="0" cellspacing="0" cellpadding="0">
       <tr>
         <td width="42%"><b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> &gt; 更改频道字段：</b></td>
         <td width="58%" align="right">
           <?php if($row['issystem']!=1){ ?>
           <input name="fset" type="button" id="fset" value="添加新字段" onClick="location.href='mychannel_field_add.php?ID=<?php echo $ID?>'" class='nbt'>
           <?php } ?>
		   &nbsp;
		   <input type="button" name="ss1" value="模型信息" style="width:70px;margin-right:6px" onClick="location='mychannel_edit.php?ID=<?php echo $ID?>&dopost=edit';" class='nbt'>
		   </td>
       </tr>
     </table></td>
   </tr>
   <tr align="center" bgcolor="#F0F9D7">
          <td width="28%" height="28" bgcolor="#FBFEED">表单提示文字</td>
          <td width="20%" bgcolor="#FBFEED">数据字段名</td>
          <td width="28%" bgcolor="#FBFEED">数据类型</td>
          <td bgcolor="#FBFEED">维护</td>
        </tr>
        <?php 
		  $ds = file(dirname(__FILE__)."/inc/fieldtype.txt");
		  foreach($ds as $d){
		    $dds = explode(',',trim($d));
			  $fieldtypes[$dds[0]] = $dds[1];
		  }
		  $fieldset = $row['fieldset'];
		  $dtp = new DedeTagParse();
	    $dtp->SetNameSpace("field","<",">");
      $dtp->LoadSource($fieldset);
      if(is_array($dtp->CTags)){
		  foreach($dtp->CTags as $ctag){
		  ?>
        <tr align="center" bgcolor="#FFFFFF">
          <td><?php 
			$itname = $ctag->GetAtt('itemname');
			if($itname=='') echo "没指定";
			else echo $itname;
			?></td>
          <td height="28"><?php echo $ctag->GetTagName()?></td>
          <td width="28%"><?php 
			$ft = $ctag->GetAtt('type');
			if(isset($fieldtypes[$ft])) echo $fieldtypes[$ft];
			else  echo "系统专用类型";
			?></td>
          <td height="28"><a href='mychannel_field_edit.php?ID=<?php echo $ID?>&fname=<?php echo $ctag->GetTagName()?>&issystem=<?php echo $row['issystem']; ?>'>[修改]</a>
              <?php if($row['issystem']!=1){ ?>
              &nbsp;<a href='#' onClick='javascript:DelNote("mychannel_field_edit.php?ID=<?php echo $ID?>&fname=<?php echo $ctag->GetTagName()?>&action=delete");'>[移除]</a>
              <?php } ?>
           </td>
        </tr>
        <?php 
		  }}
		  ?>
		  <tr align="center" bgcolor="#F0F9D7">
          <td height="28" colspan="4" bgcolor="#FBFEED">&nbsp;</td>
        </tr>
      </table>
<?php 
$dsql->Close();
?>
</body>
</html>
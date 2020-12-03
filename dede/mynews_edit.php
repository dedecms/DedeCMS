<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_站内新闻发布');
if(empty($dopost)) $dopost = "";
$aid = ereg_replace("[^0-9]","",$aid);
$dsql = new DedeSql(false);
if($dopost=="del")
{
	 $dsql->SetQuery("Delete From #@__mynews where aid='$aid';");
	 $dsql->ExecuteNoneQuery();
	 $dsql->Close();
	 ShowMsg("成功删除一条站内新闻！","mynews_main.php");
	 exit();
}
else if($dopost=="editsave")
{
	$dsql->SetQuery("Update #@__mynews set title='$title',typeid='$typeid',writer='$writer',senddate='".GetMKTime($sdate)."',body='$body' where aid='$aid';");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一条站内新闻！","mynews_main.php");
	exit();
}
$myNews = $dsql->GetOne("Select #@__mynews.*,#@__arctype.typename From #@__mynews left join #@__arctype on #@__arctype.ID=#@__mynews.typeid where #@__mynews.aid='$aid';");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>站内新闻发布</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
function checkSubmit()
{
  if(document.form1.title.value=="")
  {
     document.form1.title.focus();
     alert("标题必须设定！");
     return false;
  }
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#666666">
  <form action="mynews_edit.php" method="post" name="form1" onSubmit="return checkSubmit();">
  <input type="hidden" name="dopost" value="editsave">
  <input type="hidden" name="aid" value="<?=$myNews['aid']?>">
  <tr>
      <td height="24" background="img/tbg.gif"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td><strong>&nbsp;站内新闻管理-&gt;增加消息</strong></td>
            <td align="right"> <a href="mynews_main.php"><img src="img/file_edit.gif" width="15" height="16" border="0"><u>返回管理页</u></a> 
            </td>
          </tr>
        </table></td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
          <tr> 
            <td height="20" colspan="2">　　说明：站内新闻是为了方便站长发布站点公告而设置的一种小功能，由于要读取包括text字段的信息，应定期删除太旧的信息，否则可能会让模板解析速度变慢。如果没有选择显示频道，则在这个频道中使用这个标记时会被“所有位置...”的标记内容代替。</td>
          </tr>
          <tr> 
            <td height="20" colspan="2">　　站内新闻调用代码： {dede:mynews row='条数' titlelen='标题长度'}Innertext{/dede:mynews}，Innertext支持的字段为：[field:title 
              /],[field:writer /],[field:senddate /](时间),[field:body /]。 </td>
          </tr>
          <tr> 
            <td width="13%" height="30">标　题：</td>
            <td width="87%"> <input name="title" type="text" id="title" value="<?=$myNews['title']?>" size="30" style="width:300"> 
            </td>
          </tr>
          <tr>
            <td height="30">显示频道：</td>
            <td>
			  <select name="typeid" style="width:150">     
        <?
			  $dsql->SetQuery("Select ID,typename From #@__arctype where reID=0 order by ABS(".$myNews['typeid']." - ID) asc");
			  $dsql->Execute();
			  while($row = $dsql->GetObject())
			  {
			     echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
			  }
			  if($myNews['typeid']=="0") echo "<option value=\"0\" selected>所有位置...</option>\r\n";
			  else echo "<option value=\"0\">所有位置...</option>\r\n";
			  ?>
        </select>
			   </td>
          </tr>
          <tr> 
            <td height="30">发言人：</td>
            <td><input name="writer" type="text" id="writer" value="<?=$myNews['writer']?>" size="16">
              　 日期： 
              <input name="sdate" type="text" id="sdate" size="25" value="<?=GetDateTimeMk($myNews['senddate'])?>"></td>
          </tr>
          <tr> 
            <td height="172" valign="top">信息内容：</td>
            <td height="172"> 
              <?
	GetEditor("body",$myNews['body'],250,"Small");
	?>
            </td>
          </tr>
          <tr> 
            <td height="38">&nbsp;</td>
            <td><input type="submit" name="Submit" value="提交新闻"> &nbsp;</td>
          </tr>
          <tr bgcolor="#F1FAF2"> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </td>
</tr>
</form>
</table>
<?
$dsql->Close();
?>
</body>
</html>
<?
require("config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
require("inc_listpage.php");
require_once("inc_typelink.php");
$conn = connectMySql();
$tl = new typeLink();;
$pagesize = 10;
$bgcolor = "";
if(!isset($page)) $page="";
if(!isset($keyword)) $keyword="";
if(!isset($total)) $total="";
if(!isset($arttype)) $arttype="0";
$arttypesql = "";
$typename = "";
if($cuserLogin->getUserChannel()<=0)
	$typeCallLimit = "";
else
	$typeCallLimit = "And ".$tl->getSunID($cuserLogin->getUserChannel());
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>评论管理</title>
<script language='javascript'>
//获得选中文件的文件名
function getCheckboxItem()
{
	var allSel="";
	if(document.feedback.fid.value) return document.feedback.fid.value;
	for(i=0;i<document.feedback.fid.length;i++)
	{
		if(document.feedback.fid[i].checked)
		{
			if(allSel=="")
				allSel=document.feedback.fid[i].value;
			else
				allSel=allSel+"`"+document.feedback.fid[i].value;
		}
	}
	return allSel;	
}
function selAll()
{
	for(i=0;i<document.feedback.fid.length;i++)
	{
		document.feedback.fid[i].checked=true;
	}
}
function selNone()
{
	for(i=0;i<document.feedback.fid.length;i++)
	{
		document.feedback.fid[i].checked=false;
	}
}
function selNor()
{
	for(i=0;i<document.feedback.fid.length;i++)
	{
		if(document.feedback.fid[i].checked==false)
			document.feedback.fid[i].checked=true;
		else
			document.feedback.fid[i].checked=false;
		
	}
}
function delFeedback()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else if(window.confirm('你确定要删除这些评论吗?')) location.href="feedback_d.php?job=del&fid="+qstr;
}
function checkFeedback()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="feedback_d.php?job=check&fid="+qstr;
}
function editFeedback()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="feedback_edit.php?job=edit&fid="+qstr;
}
</script>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form name='form1'>
  <tr>
    <td height="19" background='img/tbg.gif'>
     &nbsp;<b>评论管理</b> &nbsp;关键字：<input type='text' size='15' name='keyword'>&nbsp;
     <select name="arttype">
         <option value="0" selected>--请选择--</option>
            <?
				if($cuserLogin->getUserChannel()<=0)
					$tl->GetOptionArray();
				else
					$tl->GetOptionArray($arttype,$cuserLogin->getUserChannel());
		   ?>
              </select>&nbsp;<input type='submit' name='sb' value=' 确定 '>
        &nbsp;</td>
</tr>
</form>
<form name='feedback'>
<tr>
    <td height="215" bgcolor="#FFFFFF" valign="top">
    <?
if($total == 0)
{
         if($page==0) $page=1;
         if($arttype!=0) $arttypesql = " And ".$tl->getSunID($arttype);
         $querystring = "select dede_feedback.*,dede_art.title,dede_art.typeid from dede_feedback left join dede_art on dede_feedback.artID=dede_art.ID where dede_feedback.msg like '%$keyword%' $arttypesql $typeCallLimit";
         $result = mysql_query($querystring,$conn);
         $total = mysql_num_rows($result);
}
$pre = $page-1;
$start = $pre*$pagesize;
$query = "select dede_feedback.*,dede_art.title,dede_art.typeid from dede_feedback left join dede_art on dede_feedback.artID=dede_art.ID where dede_feedback.msg like '%$keyword%' $arttypesql $typeCallLimit order by dede_feedback.ID desc limit $start,$pagesize";
$result = mysql_query($query,$conn);
?>
        <table width='100%' border='0' cellpadding='0' cellspacing='0'>
          <?
    while($row=mysql_fetch_object($result))
    {	
    	$title = $row->title;
    	$typeid = $row->typeid;
    	if($typeid!="")
    	{
    		$result2 = mysql_query("select typename from dede_arttype where ID=$typeid",$conn);
    		$row2=mysql_fetch_object($result2);
    		$typename = $row2->typename;
    	}
    ?>
          <tr <?=$bgcolor?> height='25'> 
            <td> <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
                <tr bgcolor="#F7F7F7"> 
                  <td width="12%">&nbsp;选择 
                    <input name="fid" type="checkbox" class="np" id="fid" value="<?=$row->ID?>"> 
                  </td>
                  <td width="23%">&nbsp;发言人： 
                    <?=$row->username?>
                  </td>
                  <td width="25%"> &nbsp;IP地址： 
                    <?=$row->ip?>
                  </td>
                  <td width="30%">&nbsp;时间： 
                    <?=$row->dtime?>
                  </td>
                  <td width="10%" align="center"><a href='feedback_edit.php?ID=<?=$row->ID?>'><img src="img/feedback-edit.gif" border="0" width="45" height="18"></a></td>
                </tr>
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td height="28" colspan="5">
                  <table width="98%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <?
                          if($row->ischeck>0) echo "[已审核]"; 
                          else
                          {
                          	
                          	echo "<font color='red'>[未审核]</font>";
                          }
                          ?>
                          <?=$row->msg?>
                          <br>
                          <span style='background-color:#EDFCE4'>所属文章：<?echo $title;?>&nbsp;类目：<? echo "<a href='list_feedback.php?keyword=".$keyword."&arttype=".$typeid."'><u>$typename</u></a>";?>
                          </span>
                        </td>
                      </tr>
                    </table></td>
                </tr>
              </table>
              <table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="3"></td>
                </tr>
              </table></td>
          </tr>
          <?
     }
   ?>
          <tr>
            <td height='20' align='right' bgcolor='#EAEEE1'>
              <? listpage("list_feedback.php",$total,$page,$pagesize,"&keyword=$keyword"); ?>
            </td>
          </tr>
          <tr> 
            <td height='30'>
            <table width="98%">
            <tr>
            <td width="30%">
            <input type='button' name='kk1' value='全选' onClick="selAll()" class="ll"> 
            <input type='button' name='kk2' value='取消' onClick="selNone()" class="ll">
            <input type='button' name='kk3' value='反选' onClick="selNor()" class="ll">
            </td>
            <td align="right">
            <?
            if($cuserLogin->getUserType()>1) echo "<input type='button' name='db' value=' 删除评论 ' onClick=\"delFeedback()\">\r\n";
            ?>
              &nbsp;
              <input type='button' name='db2' value=' 审核评论 ' onClick="checkFeedback()">
              </td>
            </tr>
            </table>
             </td>
          </tr>
        </table>    
    </td>
</tr>
</form>
</table>
</body>
</html>
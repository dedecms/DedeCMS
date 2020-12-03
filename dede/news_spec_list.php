<?
require("config.php");
require("inc_typeunit.php");
require("inc_page_list.php");
$conn = connectMySql();
function getFileName($stime,$ID,$typedir)
{
	global $art_dir;
	$ts = split("-",$stime);
	$fpath = $art_dir."/$typedir";
	return $fpath."/$ID.htm";
}
$ut = new typeUnit($conn,$art_dir);
$sql = "";
$sqldd="";
//读取列表的相关参数
$pagesize=20;
$sql = "Select spec.ID,spec.spectitle,spec.stime,spec.AID,arttype.typename,arttype.typedir From spec left join arttype on spec.typeid=arttype.ID where 1 ";
$sqlcount = "Select count(ID) as dd From spec where 1 ";
$pageurl = "news_spec_list.php?tag=1";
if($arttoptype!=0)
{
	$sqldd.=" And ".$ut->getSunID($arttoptype,"spec");
	$pageurl.="&arttoptype=".$arttoptype;
}
if(isset($keyword)&&$keyword!="")
{
	$sqldd.=" And spectitle like '%".$keyword."%'";
	$pageurl.="&keyword=".urlencode($keyword);
}
//----------------
$nowurl = $PHP_SELF;
$qstr = getenv("QUERY_STRING");
if($qstr!="") $nowurl.="?".$qstr;
$sql.=$sqldd;
$sqlcount.=$sqldd;       
if(!isset($total_record))
{
      $rs=mysql_query($sqlcount,$conn);
      $row=mysql_fetch_object($rs);
      $total_record = $row->dd;
}
setcookie("ENV_GOBACK_URL","$nowurl",time()+36000);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>专题管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
//获得选中文件的文件名
function getCheckboxItem()
{
	var allSel="";
	if(document.form2.artids.value) return document.form2.artids.value;
	for(i=0;i<document.form2.artids.length;i++)
	{
		if(document.form2.artids[i].checked)
		{
			if(allSel=="")
				allSel=document.form2.artids[i].value;
			else
				allSel=allSel+"`"+document.form2.artids[i].value;
		}
	}
	return allSel;	
}
function selAll()
{
	for(i=0;i<document.form2.artids.length;i++)
	{
		if(!document.form2.artids[i].checked)
		{
			document.form2.artids[i].checked=true;
		}
	}
}
function noSelAll()
{
	for(i=0;i<document.form2.artids.length;i++)
	{
		if(document.form2.artids[i].checked)
		{
			document.form2.artids[i].checked=false;
		}
	}
}
function specDel()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="news_spec_del.php?ID="+qstr;
}
function specEdit()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="news_spec_edit.php?ID="+qstr;
}
function specSend()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="news_specmake.php?ID="+qstr;
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="96%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
  <tr bgcolor="#E7E7E7"> 
    <td height="24" colspan="5"><strong>&nbsp;<u>管理已有的专题</u></strong> [<a href="add_news_spec.php"><u>专题创建向导</u></a>] 
    </td>
  </tr>
  <form name="form2">
  <tr bgcolor="#F9F8F4"> 
    <td width="9%" align="center">选取 </td>
    <td width="46%" align="center" bgcolor="#F9F8F4">标题</td>
    <td width="18%" align="center">类目</td>
    <td width="16%" align="center">日期</td>
    <td width="11%" align="center">状态</td>
  </tr>
  <?
        $sql.=$orderby.get_limit($pagesize);
        if($total_record!=0)
        {
        	$rs = mysql_query($sql,$conn);
        	while($row=mysql_fetch_object($rs))
        	{
        		$sid = $row->ID;
        		$title = $row->spectitle;
        		$aid = $row->AID;
        		$typedir = $row->typedir;
        		$typename = $row->typename;
        		$stime = $row->stime;
        		if($aid>0) $ismake="已发布";
        		else $ismake="未发布";
        		$typefile = $art_dir."/".$row->typedir;	
        ?>
  <tr bgcolor="#FFFFFF" align="center"> 
    <td><input name="artids" type="checkbox" class="np" id="artids" value="<?=$sid?>"></td>
    <td><a href='add_news_specview.php?ID=<?=$sid?>' target='_blank'><u><?=$title?></u></a></td>
    <td><a href='<?=$typefile?>' target='_blank'><?=$typename?></a></td>
    <td><?=$stime?></td>
    <td><?=$ismake?></td>
  </tr>
  <?
	}
	}
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td height="22" colspan="5">
    &nbsp;&nbsp;
    <a href="javascript:specEdit()" class="coolbg">[编辑]</a>&nbsp;
    <a href="javascript:specSend()" class="coolbg">[发布]</a>&nbsp;
    <a href="javascript:specDel()" class="coolbg">[删除]</a>&nbsp;
    </td>
  </tr>
  </form>
  <tr bgcolor="#F9F8F4" align="right"> 
    <td height="22" colspan="5"><?get_page_list($pageurl,$total_record,$pagesize);?>&nbsp;&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <form action="news_spec_list.php" name="sform" method="get">
          <tr> 
            <td height="4"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td style="border-top: 1px solid #525252;border-left: 1px solid #525252;border-right: 1px solid #525252;" height="26" align="center" background="img/tbg.gif">
            文章类目：
            <select name="arttoptype">
                <option value="0" selected>--请选择--</option>
                <?
				$ut->GetOptionArray();
				?>
              </select> &nbsp;&nbsp;
            关键字： 
            <input name="keyword" type="text" id="keyword" size="15">
            
            <input type="submit" name="Submit" value="确定"></td>
          </tr>
          <tr> 
            <td colspan="2" height="4"></td>
          </tr>
        </form>
      </table>
</body>
</html>
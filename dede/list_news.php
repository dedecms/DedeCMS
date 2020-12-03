<?
require_once("config.php");
require_once("inc_typelink.php");
require_once("inc_page_list.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$conn = connectMySql();
$tl = new typeLink();
$sql = "";
$sqldd="";
//读取列表的相关参数
$pagesize=20;
if($cuserLogin->getUserChannel()<=0)
	$typeCallLimit = "";
else
	$typeCallLimit = "And ".$tl->getSunID($cuserLogin->getUserChannel());
////////////////////////////////
$memberidsql = " And dede_art.rank>-1";

if(empty($orderid))
{
	$orderby=" order by dede_art.ID desc ";
	$orderid = "";
}
else
{
	if($orderid=="click") $orderby=" order by dede_art.click desc ";
}
if(empty($arttoptype)) $arttoptype=0;
$sql = "Select dede_art.ID,dede_art.title,dede_art.typeid,dede_art.spec,dede_art.redtitle,dede_art.ismake,dede_art.isdd,dede_art.stime,dede_art.dtime,dede_art.click,dede_arttype.typename,dede_arttype.typedir,dede_art.rank,dede_art.spec From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.redtitle<5 $typeCallLimit $memberidsql";
$sqlcount = "Select count(ID) as dd From dede_art where dede_art.redtitle<5 $typeCallLimit $memberidsql";
$pageurl = "list_news.php?tag=1";
if($arttoptype!=0)
{
	$sqldd.=" And ".$tl->getSunID($arttoptype);
	$pageurl.="&arttoptype=".$arttoptype;
}
if(!empty($keyword))
{
	$sqldd.=" And dede_art.title like '%".$keyword."%'";
	$pageurl.="&keyword=".urlencode($keyword);
}
//--------------------------------------------------------------------
if(!ereg("orderid",$dedeNowurl))
{
	if(!ereg("\?",$dedeNowurl)) $clickurl=$dedeNowurl."?orderid=click";
	else $clickurl=$dedeNowurl."&orderid=click";
}
else
{
	$clickurl=ereg_replace("[\&\?]$","",str_replace("orderid=click","",$dedeNowurl));
}
if($orderid!="") $pageurl.="&orderid=$orderid";
$sql.=$sqldd;
$sqlcount.=$sqldd;       
if(empty($total_record))
{
      $rs=mysql_query($sqlcount,$conn);
      $row=mysql_fetch_object($rs);
      $total_record = $row->dd;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>管理文章</title>
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
function makeHtml()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="make_news_html.php?artids="+qstr;
}
function makeCheck()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="make_news_check.php?artids="+qstr;
}
function makeLike()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="make_news_like.php?artids="+qstr;
}
function makeSpec()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="add_news_spec.php?artids="+qstr;
}
function setRank()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="make_news_rank.php?artids="+qstr;
}
function setPic()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else if(qstr.indexOf("`")>0) alert("这项操作不允许多选！");
	else location.href="add_news_ok.php?artID="+qstr;
}
function delFile()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="art_del.php?artids="+qstr;
}
function makeEdit()
{
	var qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else location.href="news_edit.php?artID="+qstr;
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='2'>
<table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td height='425' align='center' valign='top'>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#666666">
		<tr> 
            <td height="24" background="menu/newlinebg2.gif">
            <table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <form name="form1" action="make_news_html_id.php">
				  <td width="10%">&nbsp;更新HTML</td>
                  <td width="8%">起始ID：</td>
                  <td width="10%"><input name="startid" type="text" id="startid" size="8" style="width:50;height:18"></td>
                  <td width="8%" align="right">结束ID：</td>
                  <td width="8%"><input name="endid" type="text" id="endid" size="8" style="width:50;height:18"></td>
                  <td width="6%" align="right">分段：</td>
                  <td width="8%"><input name="pagenum" type="text" size="8" style="width:50;height:18" value="200"></td>
                  <td width="10%" align="center"> 
                    <input type="submit" name="Submit2" value="提交" style="width:40;height:18">
                  </td>
				  </form>
                  <td align="right">
                  <a href="add_news_view.php?typeid=<?=$arttoptype?>" style="border:1px solid #000000;background-color:#FCFBEB;width:54;height:16">添加文章</a>
                  <a href="list_news_member.php" style="border:1px solid #000000;background-color:#FCFBEB;width:54;height:16">稿件审核</a>
                  <a href="list_type.php" style="border:1px solid #000000;background-color:#FCFBEB;width:54;height:16">频道管理</a>
                  </td>
                </tr>
              </table>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr bgcolor="#FFFFFF"><td height="4"></td></tr>
    </table>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#666666">
		<tr bgcolor="#E7E7E7"> 
            <td height="24" colspan="9" background="img/tbg.gif">
			&nbsp;<strong>&nbsp;§文章列表</strong> </td>
            </td>
        </tr>
		<form name="form2">
        <tr align="center" bgcolor="#FAFAF1"> 
          <td width="5%" height="18">选择</td>
          <td width="30%" height="18">&nbsp;文章标题</td>
          <td width="15%">发布时间</td>
          <td width="11%">类目</td>
          <td width="8%"><a href='<?=$clickurl?>'><u>点击</u></a></td>
          <td width="5%">图片</td>
          <td width="7%">HTML</td>
            <td width="12%" height="18">等级</td>
            <td width="6%">类型</td>
        </tr>
        <?
        $sql.=$orderby.get_limit($pagesize);
        if($total_record!=0)
        {
        	$rs = mysql_query($sql,$conn);
        	while($row=mysql_fetch_object($rs))
        	{
        		$ID = $row->ID;
        		$title = $row->title;
        		$btypeid = $row->typeid;
        		$tl->setTypeID($btypeid);
        		$picture = $row->isdd;
        		$dtime = $row->stime;
        		$typename = $row->typename;
        		$ismake = $row->ismake;
        		$typefile = $art_dir."/".$row->typedir;
        		if($picture>0) $picture = "<font color='red'>[有]</font>";   
        		else $picture = "[无]";
        		if($ismake>0) $ismaken = "已生成";   
        		else $ismaken = "未生成";
        		$click = $row->click;	
        		$rank=$row->rank;
        		$rs2 = mysql_query("Select * from dede_membertype where rank='$rank'",$conn);
        		$row2 = mysql_fetch_array($rs2);
        		$rankn = cn_substr($row2[2],8);
        		$linkfile=$tl->getFileName($row->ID,$row->typedir,$row->stime,$row->rank);
        ?>
        <tr bgcolor="#FFFFFF" height="18"> 
          <td align='center'><input name="artids" type="checkbox" class="np" id="artids" value="<?=$ID?>"></td>
          <td><a href='<?=$linkfile?>' target='_blank'><?=$title?></a></td>
          <td align='center'><?=$dtime?></td>
          <td align='center'><a href='<?=$typefile?>' target='_blank'><?=$typename?></a></td>
          <td align='center'><?=$click?></td>
          <td align='center'><?=$picture?></td>
          <td align='center'><?=$ismaken?></td>
          <td align='center'><?=$rankn?></td>
          <td align='center'>
             <?
			 if($row->redtitle==2) $mt="<b>专题</b>";
			 else if($row->redtitle > 0) $mt="<b>推荐</b>";
			 else $mt="普通";
			 if($row->isdd>0&&$row->redtitle!=2) $mt="<font color='red'>图片</font>";
			 echo $mt;
			 ?>
          </td>
        </tr>
        <?
               }
        }
        ?>
        <tr  bgcolor="#FAFAF1"> 
            <td height="24" colspan="9"> &nbsp;<a href="javascript:selAll()" class="coolbg">全选</a> 
              <a href="javascript:noSelAll()" class="coolbg">取消</a> 
              &nbsp;<a href="javascript:makeLike()" class="coolbg">[文章关连]</a> 
              &nbsp;<a href="javascript:setRank()" class="coolbg">[更改级别]</a> &nbsp;<a href="javascript:setPic()" class="coolbg">[设置缩略图]</a>&nbsp;
              <a href="javascript:makeEdit()" class="coolbg">[编辑]</a>&nbsp;
              <a href="javascript:makeEdit()" class="coolbg">[审核]</a>&nbsp;
              <a href="javascript:makeHtml()" class="coolbg">[重新发布]</a>&nbsp;
             <? if($cuserLogin->getUserType()==10) echo "<a href='javascript:delFile()' class='coolbg'>[删除]</a>";?>&nbsp; 
              </td>
        </tr>
		</form>
        <tr align="right" bgcolor="#E7E7E7"> 
          <td height="20" colspan="9"> 
            <?
          get_page_list($pageurl,$total_record,$pagesize);
          ?>
            &nbsp;&nbsp;</td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <form action="list_news.php" name="form1" method="get">
          <tr> 
            <td height="4"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td style="border: 1px solid #525252;" height="26" align="center" background="img/tbg.gif">
            文章类目：
            <select name="arttoptype">
                <option value="0" selected>--请选择--</option>
                <?
				if($cuserLogin->getUserChannel()<=0)
					$tl->GetOptionArray();
				else
					$tl->GetOptionArray(0,$cuserLogin->getUserChannel());
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
      </table></td>
  </tr>
</table>
</body>

</html>

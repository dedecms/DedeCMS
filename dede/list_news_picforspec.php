<?
require("config.php");
require("inc_typelink.php");
require("inc_page_list.php");
$conn = connectMySql();
$ut = new TypeLink();
//读取分类时用到的临时变量
$sql = "";
$sqldd="";
//读取列表的相关参数
$pagesize=20;
$orderby=" order by dede_art.ID desc ";
$sql = "Select dede_art.ID,dede_art.title,dede_art.typeid,dede_art.isdd,dede_art.stime,dede_art.click,dede_arttype.typename,dede_arttype.typedir,dede_art.rank,dede_art.spec,dede_art.picname From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where  dede_art.isdd=1 And dede_art.rank>=0 ";
$sqlcount = "Select count(ID) as dd From dede_art where dede_art.isdd=1 and dede_art.rank>=0 ";
$pageurl = "list_news_picforspec.php?tag=1";

if(empty($arttoptype)) $arttoptype="";
if($arttoptype==0||$arttoptype=="")
{
	$sids = $ut->GetSunID($cuserLogin->getUserChannel(),"dede_art",1);
	if($sids!="") $sqldd.=" And $sids";
}
else
{
	$sids = $ut->GetSunID($arttoptype,"dede_art",1);
	if($sids!="") $sqldd.=" And $sids";
	$pageurl.="&arttoptype=".$arttoptype;
}

if(isset($keyword))
{
	$sqldd.=" And title like '%".$keyword."%'";
	$pageurl.="&keyword=".urlencode($keyword);
}
$sql.=$sqldd;
$sqlcount.=$sqldd;       
if(!isset($total_record))
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
			allSel=document.form2.artids[i].value;
		}
	}
	return allSel;	
}
function reValue(getstr)
{
	var qstr,qstrs;
    qstrs = new Array(3);
    if(getstr!="") qstr = getstr;
    else qstr=getCheckboxItem();
	if(qstr=="") alert("你没选中任何内容！");
	else
	{
		qstrs = qstr.split("`");
		window.opener.document.form1.imglink.value=qstrs[0];
		window.opener.document.form1.imgtitle.value=qstrs[1];
		window.opener.document.form1.specimg.value=qstrs[2];
		window.opener=true;
    	window.close();
    }
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='6'>
<table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td height='425' align='center' valign='top'>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#666666">
        <form name="form2">
		<tr bgcolor="#E7E7E7"> 
          <td height="20" colspan="7" background="img/tbg.gif"><strong>&nbsp;§文章列表</strong>(如果你要的文章找不到,请在“文章管理”处把文章改为图片新闻，并设置缩略图)</td>
        </tr>
        <tr align="center" bgcolor="#FAFAF1"> 
          <td width="5%" height="18">选择</td>
          <td width="36%" height="18">&nbsp;文章标题</td>
          <td width="12%">发布时间</td>
          <td width="12%">类目</td>
            <td width="10%" height="18">等级</td>
            <td width="10%">类型</td>
            <td width="15%">图片</td>
        </tr>
        <?
        $sql.=$orderby.get_limit($pagesize);
        if($total_record!=0)
        {
        	$rs = mysql_query($sql,$conn);
        	while($row=mysql_fetch_object($rs))
        	{
        		$ID = $row->ID;
        		$title = trim($row->title);
        		$btypeid = $row->typeid;
        		$picture = $row->isdd;
        		$dtime = $row->stime;
        		$typename = $row->typename;
        		$typefile = $art_dir."/".$row->typedir;
        		if($picture>0) $picture = "<font color='red'>[有]</font>";   
        		else $picture = "[无]";
        		$click = $row->click;	
        		$rank=$row->rank;
        		$rs2 = mysql_query("Select * from dede_membertype where rank='$rank'",$conn);
        		$row2 = mysql_fetch_array($rs2);
        		$rankn = cn_substr($row2[2],8);
        		$linkfile=$ut->GetFileName($ID,$row->typedir,$row->stime,$row->rank);
        ?>
        <tr bgcolor="#FFFFFF" height="18"> 
          <td align='center'><input name="artids" type="radio" class="np" id="artids" value="<?=$linkfile."`".$title."`".$row->picname?>"></td>
          <td><a href='<?=$linkfile?>' target='_blank'><?=$title?></a></td>
          <td align='center'><?=$dtime?></td>
          <td align='center'><a href='<?=$typefile?>'><?=$typename?></a></td>
          <td align='center'><?=$rankn?></td>
          <td align='center'>
             <?
			 if($row->spec > 0) echo "专题";
			 else echo "文章";
			 ?>
          </td>
          <td align='center'><a href='#' onClick="reValue('<?=$linkfile."`".$title."`".$row->picname?>')"><img src='<?=$row->picname?>' width='60' height='60' border='0'></a></td>
        </tr>
        <?
               }
        }
        ?>
        <tr  bgcolor="#FAFAF1"> 
            <td height="24" colspan="7">
            &nbsp;<input type='button' name='b2' value=" 确定 " onClick="reValue('')">
            </td>
        </tr>
		</form>
        <tr align="right" bgcolor="#E7E7E7"> 
          <td height="20" colspan="7"> 
            <?
          get_page_list($pageurl,$total_record,$pagesize);
          ?>
            &nbsp;&nbsp;</td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" method="get">
          <tr> 
            <td height="4"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td style="border: 1px solid #525252" height="26" align="center" background="img/tbg.gif">
            文章类目：
                <select name="arttoptype">
                <option value="0" selected>--请选择--</option>
                <?
				if($cuserLogin->getUserChannel()<=0)
					$ut->GetOptionArray(0,0,1);
				else
					$ut->GetOptionArray(0,$cuserLogin->getUserChannel(),1);
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
</table></body></html>

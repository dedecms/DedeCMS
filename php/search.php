<?
require_once("config.php");
require_once("inc_page_list.php");
$conn = connectMySql();
$sql = "";
$sqldd="";
if(empty($keyword)) $keyword = "";
if(empty($tag)) $tag = 1;
$keyword = trim($keyword);
//读取列表的相关参数
$pagesize=15;
$orderby=" order by dede_art.ID desc ";
if(!isset($typeid)) $typeid=0;
$sql = "Select dede_art.ID,dede_art.title,dede_art.typeid,dede_art.msg,dede_art.isdd,dede_art.stime,dede_art.click,dede_arttype.typename,dede_arttype.typedir,dede_art.rank,dede_art.spec From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 ";
$sqlcount = "Select count(ID) as dd From dede_art where dede_art.rank>=0 ";
$pageurl = "search.php?keyword=$keyword&tag=$tag&typeid=$typeid";
if($tag==0) $sqldd.=" and (dede_art.title like '%$keyword%' or dede_art.msg like '%$keyword%')";
if($tag==1) $sqldd.=" and (dede_art.title like '%$keyword%')";
if($tag==2) $sqldd.=" and (dede_art.title like '%$keyword%' or dede_art.msg like '%$keyword%' or dede_art.body like '%$keyword%')";
if($typeid!=0) $sqldd.=" and (dede_art.typeid=$typeid)";
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
<title>文章搜索</title>
<link href='../base.css' rel='stylesheet' type='text/css'>
</head>
<body leftmargin='6' topmargin='6'>
<table width='760' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td  align='center' valign='top'>
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
        <form name="form2">
          <tr bgcolor="#E7E7E7"> 
            <td width="64%" height="25" background="img/lbg.gif"><strong>&nbsp;搜索结果</strong></td>
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
        		$picture = $row->isdd;
        		$dtime = $row->stime;
        		$typename = $row->typename;
        		$typefile = $art_dir."/".$row->typedir;
        		$click = $row->click;	
        		$rank=$row->rank;
        		$linkfile=GetFileName($dtime,$ID,$row->typedir,$rank);
        ?>
          <tr bgcolor="#FFFFFF" height="22"> 
            <td> <table border='0' width='100%'>
                <tr height='24'> 
                  <td width='3%'><img src='img/file.gif' width='18' height='17'></td>
                  <td width='60%'><a href='<?=$linkfile?>' target='_blank'> 
                    <?=$title?>
                    </a></td>
                  <td width='15%'>类别：<a href='<?=$typefile?>' target='_blank'> 
                    <?=$typename?>
                    </a></td>
                  <td width='22%'>点击: 
                    <?=$click?>
                  </td>
                </tr>
                <tr> 
                  <td height='2' colspan='4' background='img/writerbg.gif'></td>
                </tr>
                <tr> 
                  <td colspan='4'>
                    <?=$row->msg?><font color='#8F8C89'>(<?=$row->stime?>)</font> </td>
                </tr>
              </table></td>
          </tr>
          <?
               }
        }
        ?>
        </form>
        <tr align="right" bgcolor="#E7E7E7"> 
          <td height="20" bgcolor="#F3F7E8"> 
            <?
          get_page_list($pageurl,$total_record,$pagesize);
          ?>
            &nbsp;&nbsp;</td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <form action="search.php" name="form1" method="get">
          <tr> 
            <td height="4"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="26" align="center" background="img/lbg.gif"> 
              搜索类目： 
              <select name="typeid">
                <option value="0" selected>--请选择--</option>
                <?
				$tl = new TypeLink();
				$tl->GetOptionArray($typeid,0,0);
				?>
              </select> &nbsp;
			  
			  搜索范围： 
              <select name="tag" id="tag">
                      <option value="0" selected>默认搜索</option>
                      <option value="1">仅搜索标题</option>
                      <option value="2">全文搜索</option>
            </select> &nbsp;
            关键字： 
            <input name="keyword" type="text" id="keyword" size="15">
            
            <input type="submit" name="Submit" value="确定">
            </td>
          </tr>
          <tr> 
            <td colspan="2" height="4"></td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
<div align="center"><br>
  <a href="http://www.dedecms.com" target="_blank">Power by DedeCms.com</a> </div>
</body>

</html>

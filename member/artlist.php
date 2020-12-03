<?
require("config.php");
require_once("inc_page_list.php");
require_once("inc_unit.php");
if(empty($userSendArt)) $userSendArt=-1;
if(empty($_COOKIE["cookie_rank"])) $userrank=-1000;
else $userrank=$_COOKIE["cookie_rank"];
if($userSendArt!=-1 && $userrank < $userSendArt)
{
	ShowMsg("权限不足！","back");
	exit();
}
$conn = connectMySql();
$pagesize=20;
$sql = "Select dede_art.ID,dede_art.title,dede_art.rank,dede_art.stime,dede_art.dtime,dede_arttype.typename,dede_arttype.typedir From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where memberID=".$_COOKIE["cookie_user"];
$sqlcount = "Select count(ID) as dd From dede_art where memberID=".$_COOKIE["cookie_user"];
$pageurl = "artlist.php";     
if(!isset($total_record))
{
      $rs=mysql_query($sqlcount,$conn);
      $row=mysql_fetch_object($rs);
      $total_record = $row->dd;
}
$orderby = " order by dede_art.ID desc ";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>文章管理</title>
<link href="../base.css" rel="stylesheet" type="text/css">	
</head>
<body leftmargin="0" topmargin="0">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF"> 
    <td height="50" colspan="4"><img src="img/member.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bgcolor="#808DB5" width="30">&nbsp;</td>
    <td bgcolor="#808DB5" width="220">&nbsp;</td>
    <td bgcolor="#808DB5" width="250">&nbsp;</td>
    <td width="200" align="right">
	<a href="index.php"><u>管理中心</u></a>
	<a href="/"><u>网站首页</u></a>
    <a href="exit.php?job=all"><u>退出登录</u></a></td>
  </tr>
  <tr> 
    <td width="30" bgcolor="#808DB5">&nbsp;</td>
    <td colspan="3" rowspan="2" valign="top">
	<table width="100%" height="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
        <tr> 
          <td height="100" align="center" valign="top" bgcolor="#FFFFFF">
		  <table width="98%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="6" colspan="2"></td>
              </tr>
              <tr> 
                <td width="84%" height="24" valign="bottom"><strong>管理稿件： </strong></td>
                <td width="16%" valign="bottom">[<a href="artsend.php">发表新文章</a>]</td>
              </tr>
              <tr> 
                <td colspan="2" align="center"><hr size="1"></td>
              </tr>
              <tr>
                <td height="22" colspan="2"><table width="100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#859683">
                  <tr align="center" bgcolor="#DCE0DA">
                    <td width="35%">文章名称</td>
                    <td width="18%">文章类别</td>
                    <td width="22%">时间</td>
                    <td width="10%">状态</td>
                    <td width="15%">管理</td>
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
        			$typename = $row->typename;
        			$dtime = $row->dtime;
        			$rank = $row->rank;
        			if($rank==-1)
        			{
        				$str="未审核";
        				$modstr = "<a href='artedit.php?ID=$ID'>[编辑]</a> <a href='artdel.php?ID=$ID'>[移除]</a>";
        			}
        			else
        			{
        				$str="已审核";
        				$modstr = "不允许编辑";
        			}
        			$filelink = GetFileName($ID,$row->typedir,$row->stime,$rank);
        		?>
                  <tr align="center" bgcolor="#FFFFFF">
                    <td><a href='<?=$filelink?>' target='_blank'><?=$title?></a></td>
                    <td><?=$typename?></td>
                    <td><?=$dtime?></td>
                    <td><?=$str?></td>
                    <td><?=$modstr?></td>
                  </tr>
                  <?
               		 }
                	}
                  ?>
                  <tr align="right" bgcolor="#EBEFE9">
                    <td colspan="5"><?get_page_list($pageurl,$total_record,$pagesize);?>&nbsp; </td>
                    </tr>
                </table></td>
              </tr>
              <tr> 
                <td height="22" colspan="2">&nbsp;</td>
              </tr>
            </table>
		  </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#808DB5">&nbsp;</td>
  </tr>
</table>
<p align='center'><a href='http://www.dedecms.com'target='_blank'>Power by DedeCms 织梦内容管理系统</a></p>
</body></html>

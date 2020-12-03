<?
require("config.php");
/*
这个文件用于站点信息统计
调用:
<script language="javascript" src="/php/tj.php"></script>
效果如下：
--------------------------
文章总数：32
注册会员：33
普通文章： 32
图片集： 0
软件下载： 0
Flash： 0
*/
$conn = connectMySql();
$rs = mysql_query("select count(ID) as tjnum from dede_art",$conn);
$row = mysql_fetch_object($rs);
$tjnum = $row->tjnum;
echo "document.write(\"文章总数：$tjnum<br>\");\r\n";
$rs = mysql_query("select count(ID) as tjnum from dede_member",$conn);
$row = mysql_fetch_object($rs);
$tjnum = $row->tjnum;
echo "document.write(\"注册会员：$tjnum<br>\");\r\n";
$rs = mysql_query("select * from dede_channeltype",$conn);
while($row = mysql_fetch_object($rs))
{
  $ID = $row->ID;
  $cname = $row->typename;
  $rs2 = mysql_query("select count(dede_art.ID) as tjnum from dede_arttype left join dede_art on dede_art.typeid=dede_arttype.ID where dede_arttype.channeltype=$ID",$conn); 
  $tjnum=0;
  while($row2 = mysql_fetch_object($rs2))
  {$tjnum+=$row2->tjnum;}
  echo "document.write(\"$cname"."："." $tjnum<br>\");\r\n";
}
?>
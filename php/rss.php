<?
/////////////////////////////////////////////
//调用这个页面的方法：
//rss.php?ID=类别ID
//作用是返回最新的100条记录的rss协议形式的链接
/////////////////////////////////////////////
require("config.php");
if(empty($typeid)){ echo "类目ID无效!";exit();}
header("content-type: text/xml");
$conn = connectMySql();
$rs = mysql_query("Select typename,typedir,isdefault from dede_arttype where ID=$typeid",$conn);
$row = mysql_fetch_object($rs);
if($row->isdefault=="1")
	$typelink = $art_dir."/".$row->typedir;
else
	$typelink = $art_dir."/".$row->typedir."/list_".$typeid."_1'".$art_shortname;
/////////////////////////////////
$typename = $row->typename;
////////////////////////////////////
$qc = "?";
$row = "";
$tl = new TypeLink($typeid);
$typesql = $tl->GetSunID();
$query = "Select dede_art.ID,dede_art.title,dede_art.msg,dede_art.writer,dede_art.source,dede_art.stime,dede_art.rank,dede_art.click,dede_art.picname,dede_arttype.typedir,dede_arttype.typename,dede_arttype.isdefault From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 And $typesql order by dede_art.ID desc limit 0,100";
$rs = mysql_query($query,$conn);
echo "<".$qc."xml version=\"1.0\" encoding=\"gb2312\"".$qc.">\r\n";
echo "<rss version=\"2.0\">\r\n";
echo "<channel>\r\n";
echo "<title>$typename</title>\r\n";
echo "<link>$base_url$typelink</link>\r\n";
echo "<description>$webname 的 $typename 分类的最新内容</description>\r\n";
echo "<language>zh-cn</language>\r\n";
echo "<generator>power by www.dedecms.com</generator>\r\n";
echo "<webmaster>$admin_email</webmaster>\r\n";
while($row = mysql_fetch_object($rs))
{
	$title = htmlspecialchars(trim($row->title));
	$typename = htmlspecialchars($row->typename);
	$writer = htmlspecialchars($row->writer);
	$source = htmlspecialchars($row->source);
	$msg = htmlspecialchars(cn_substr(trim($row->msg),250))."...";
	$stime = $row->stime;
	$filelink = getFileName($stime,$row->ID,$row->typedir,$row->rank);
	echo "<item>
    <title>$title</title>
    <link>$base_url$filelink</link>
    <description>$msg</description>
    <pubDate>$stime</pubDate>
    <category>$typename</category>
    <author>$writer</author>
    <comments>出处：$source</comments>
</item>\r\n";
}
echo "</channel>\r\n";
echo "</rss>\r\n";
?>
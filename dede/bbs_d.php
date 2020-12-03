<?
require("config.php");
$conn = connectMySql();
$rs = mysql_query("Select uID,re,sdtime From bbs where ID=$ID",$conn);
$row = mysql_fetch_object($rs);
$user = $row->uID;
$re = $row->re;
$dtime = $row->sdtime;
function getname($dtime,$uID)
{
	list($d,$s) = split(" ",$dtime);
	list($y,$m,$d) = split("-",$d);
	list($h,$mm,$s) = split(":",$s);
	return("../".$bbs_dir."/upimg/$y/$m/$d/$uID$h$mm$s.jpg");
}
if(file_exists(getname($dtime,$user))) unlink(getname($dtime,$user));
$dquery = "Delete From bbs where ID=$ID";
mysql_query($dquery,$conn);
if($re>0)
{
	$rs = mysql_query("Select ID,uID,sdtime From bbs where reID=$ID",$conn);
        while($row=mysql_fetch_object($rs))
        {
        	$rID = $row->ID;
        	$user = $row->uID;
                $dtime = $row->sdtime;
                if(file_exists(getname($dtime,$user))) unlink(getname($dtime,$user));
                mysql_query("Delete From bbs where ID=$rID",$conn);
        }
}
echo "<script>\n";
echo "alert('成功删除一贴子!');\n";
echo "history.go(-1)";
echo "</script>\n";
?>

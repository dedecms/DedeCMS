<?
require_once("config.php");
require_once("../dede/inc_makeart.php");
if(isset($artID)) $ID=$artID;
if(!isset($ID))
{
	echo "指定的文章不存在！";
	exit;
}
//检测会员权限
$conn = connectMySql();
$rs = mysql_query("select dede_art.title,dede_art.msg,dede_art.rank,dede_membertype.membername from dede_art left join dede_membertype on dede_art.rank=dede_membertype.rank where dede_art.ID=$ID",$conn);
$row = mysql_fetch_array($rs);
$sta = CheckUser($row["rank"]);
//如果用户没权限
if($sta==0)
{
$body = "";
$body .= "你要查看的文章是:".$row["title"];
$body .= "<br>文章简介：".$row["msg"]."<br><br>这篇文章是 <font color='red'>".$row["membername"];
$body .= "</font> 文章，你的权限不足，无法查看！<br>";
$body .= "如果你已经升级为这个级别的会员，";
$body .= "请点击此重新<a href='/member/login.php'><u>登录</u></a>";
$body .= "<br><br><a href='javascript:history.go(-1);'><u>点击此返回上一页</u></a>";
echo $body;
exit();
}
//////////////正常情况返回的内容///////////
if(!isset($page)) $page=0;
$mr = new makeArt();
echo $mr->makeArtView($ID,$page);
?>
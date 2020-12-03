<?
include("config.php");
$conn = connectMySql();
$user = ereg_replace("[^0-9]","",$ID);
$sql = "Update dede_member Set sex=sex,age='$age',birthday='$birthday',weight='$weight',height='$height',job='$job',aera=aera,city='$city',myinfo='$myinfo',mybb='$mybb',oicq='$oicq',tel='$tel',homepage='$homepage' where ID=$user";
@mysql_query($sql,$conn);
echo "<script>\n";
echo "alert('更改一份资料成功!');\n";
echo "history.go(-1);\n";
echo "</script>\n";
exit();
?>
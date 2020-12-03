<?
if(is_dir("setup"))
{
  echo "如果你还没安装本程序,请运行<a href='setup/setup.php'>setup/setup.php</a>,否则请删除这个文件夹!";
  exit();
}
require("dede/inc_makepartcode.php");
$maprt= new MakePartCode();
$modfilename = $base_dir."/".$mod_dir."/主页向导/绿茵世界.htm";
$fp = fopen($modfilename,"r");
$testcode = fread($fp,filesize($modfilename));
fclose($fp);
echo $maprt->ParTemp($testcode);
?>
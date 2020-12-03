<?
require("config.php");
//这个文件仅作为软件下载的一个接口,
//除了转址和解密base64编码外,并没有做任何事情
if(!isset($artID)) echo "链接地址不合法!";
else if(!isset($goto)) echo "出错，无法找到指定软件！";
else
{
	$goto = base64_decode($goto);
	$goto = substr($goto,9);
	header("location:$goto");
}
?>
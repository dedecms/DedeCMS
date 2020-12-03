<?
require_once(dirname(__FILE__)."/../include/inc_arcspec_view.php");
$specfile = dirname(__FILE__)."spec_1".$art_shortname;
//如果已经编译静态列表，则直接引入第一个文件
if(file_exists($specfile))
{
	include($specfile);
	exit();
}
else
{
  $sp = new SpecView();
  $sp->Display();
  $sp->Close();
}
?>
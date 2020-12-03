<?php 
if(!isset($art_shortname)) $art_shortname = '';
$specfile = dirname(__FILE__)."spec_1".$art_shortname;
//如果已经编译静态列表，则直接引入第一个文件
if(file_exists($specfile))
{
	include($specfile);
	exit();
}
else
{
  require_once(dirname(__FILE__).'/../include/config_base.php');
  require_once(DEDEINC.'/inc_arcspec_view.php');
  $sp = new SpecView();
  $sp->Display();
  $sp->Close();
}
?>
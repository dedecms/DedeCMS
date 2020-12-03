<?php 
if(empty($selv)) $selv = '0';
function GetTypeidSelMember($fname,$fv,$fbt,$channelid,$selv='0',$selname='ÇëÑ¡Ôñ...',$opall=0){
  global $cfg_mainsite,$cfg_phpurl,$cfg_cmspath;
  $rstr  = "<script language='javascript' src='{$cfg_phpurl}/plus.js'></script>\r\n";
  $rstr .= "<input type=\"hidden\" name=\"$fv\" value=\"$selv\">\r\n";
	$rstr .= "<input type=\"button\" name=\"$fbt\" value=\"$selname\"";
	$rstr .= " style=\"height:21px;width:150px;border:0px;background-image:url({$cfg_phpurl}/img/ctbg.gif);padding-top:2px; background-color: transparent\"";
  $rstr .= " onClick=\"SelectCatalog('$fname','$fv','$fbt',$channelid,'$opall','{$cfg_mainsite}{$cfg_cmspath}/include');\">\r\n";
	return $rstr;
}
?>
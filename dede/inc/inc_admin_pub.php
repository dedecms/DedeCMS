<?php
//栏目选择
function GetTypeidSel($fname,$f1,$f2,$cc,$selv='0',$selname='请选择...',$pos='')
{
	global $opall;
	if(empty($opall))
	{
		$opall = 0;
	}
	$rstr = "<input type=\"hidden\" name=\"$f1\" value=\"$selv\">\r\n";
	$rstr .= "<input type=\"button\" name=\"$f2\" value=\"$selname\" style=\"height:21px;width:150px;border:0px;background-image:url({$pos}img/ctbg.gif);padding-top:2px; background-color: transparent\" onClick=\"SelectCatalog('$fname','$f1','$f2',$cc,'$pos','$opall');\">\r\n";
	return $rstr;
}
?>
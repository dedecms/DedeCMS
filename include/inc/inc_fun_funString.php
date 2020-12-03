<?php 
$GLOBALS['__funString'] = 1;

function SpHtml2Text($str){
  $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
  $alltext = '';

  $start = 1;
  
  for($i=0;$i<strlen($str);$i++){
    if($start==0 && $str[$i]==">") $start = 1;
    else if($start==1){
      if($str[$i]=="<"){ $start = 0; $alltext .= " "; }
      else if(ord($str[$i])>31) $alltext .= $str[$i];
    }
  }
  $alltext = str_replace("　"," ",$alltext);
  $alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
  $alltext = preg_replace("/[ ]+/s"," ",$alltext);

  return $alltext;
}

function Spcnw_mid($str,$start,$slen){
  $str_len = strlen($str);
  $strs = Array();
  for($i=0;$i<$str_len;$i++){
  	if(ord($str[$i])>0x80){
  		if($str_len>$i+1) $strs[] = $str[$i].$str[$i+1];
  		else $strs[] = '';
  	  $i++;
  	}
  	else{ $strs[] = $str[$i]; }
  }
  $wlen = count($strs);
  if($wlen < $start) return "";
  $restr = "";
  $startdd = $start;
  $enddd = $startdd + $slen;
  for($i=$startdd;$i<$enddd;$i++){
  	if(!isset($strs[$i])) break;
  	$restr .= $strs[$i];
  }
  return $restr;
}
?>
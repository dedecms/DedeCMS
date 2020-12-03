<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
header("Content-Type: text/html; charset={$cfg_ver_lang}");
if($_POST){
	$isMagic = @ini_get("magic_quotes_gpc");
	if($isMagic) foreach($_POST AS $key => $value) $$key = stripslashes($value);
	else foreach($_POST AS $key => $value) $$key = $value;
	if($reggo==0){
	   $rs = preg_replace("/$testrule/$testmode",$rpvalue,$testtext);
	   echo "<xmp>[".$rs."]</xmp>";
  }else{
  	 $backarr = array();
  	 preg_match_all("/$testrule/$testmode",$testtext,$backarr);
  	 echo "<xmp>";
  	 foreach($backarr as $k=>$v){
  	 	  echo "$k";
  	 	  if(!is_array($v)) echo " - $v \r\n";
  	 	  else{
  	 	  	 echo " Array \r\n";
  	 	  	 foreach($v as $kk=>$vv){ echo "----$kk - $vv \r\n"; }
  	 	  }
  	 }
  	 echo "</xmp>";
  }
	exit();
}


require_once(dirname(__FILE__)."/templets/ruletest.htm");

ClearAllLink();
?>

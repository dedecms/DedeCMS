<?php 
$GLOBALS['__funAdmin'] = 1;

function SpGetPinyin($str,$ishead=0,$isclose=1){
	global $pinyins;
	$restr = "";
	$str = trim($str);
	$slen = strlen($str);
	if($slen<2) return $str;
	if(count($pinyins)==0){
		$fp = fopen(dirname(__FILE__)."/../data/pinyin.db","r");
		while(!feof($fp)){
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++){
		if(ord($str[$i])>0x80)
		{
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c])){
				if($ishead==0) $restr .= $pinyins[$c];
				else $restr .= $pinyins[$c][0];
			}else $restr .= "_";
		}else if( eregi("[a-z0-9]",$str[$i]) ){	$restr .= $str[$i]; }
		else{ $restr .= "_";  }
	}
	if($isclose==0) unset($pinyins);
	return $restr;
}

function SpCreateDir($spath,$siterefer="",$sitepath=""){
	if($spath=="") return true;
	global $cfg_dir_purview,$cfg_basedir,$cfg_ftp_mkdir;
	$flink = false;
	if($siterefer==1) $truepath = ereg_replace("/{1,}","/",$cfg_basedir."/".$sitepath);
	else if($siterefer==2){
		$truepath = $sitepath;
		if($isSafeMode||$cfg_ftp_mkdir=='是'){ echo "在PHP安全模式中，无法启用对文件保存在主站以外目录的子站点的支持！"; exit(); }
	}
	else $truepath = $cfg_basedir;
	$spaths = explode("/",$spath);
	$spath = "";
	foreach($spaths as $spath){
		if($spath=="") continue;
		$spath = trim($spath);
		$truepath .= "/".$spath;
		$truepath = str_replace("\\","/",$truepath);
		$truepath = ereg_replace("/{1,}","/",$truepath);
		if(!is_dir($truepath) || !is_writeable($truepath)){
			 if(!is_dir($truepath)) $isok = MkdirAll($truepath,777);
			 else $isok = ChmodAll($truepath,777);
			 if(!$isok){ echo "创建或修改目录：".$truepath." 失败！<br>"; CloseFtp(); return false; }
		}
	}
	CloseFtp();
	return true;
}

function SpGetEditor($fname,$fvalue,$nheight="350",$etype="Basic",$gtype="print",$isfullpage="false")
{
	if(!isset($GLOBALS['cfg_html_editor'])) $GLOBALS['cfg_html_editor']='fck';
	if($gtype=="") $gtype = "print";
	if($GLOBALS['cfg_html_editor']=='fck'){
	  require_once(dirname(__FILE__)."/../FCKeditor/fckeditor.php");
	  $fck = new FCKeditor($fname);
	  $fck->BasePath		= $GLOBALS['cfg_cmspath'].'/include/FCKeditor/' ;
	  $fck->Width		= '100%' ;
	  $fck->Height		= $nheight ;
	  $fck->ToolbarSet	= $etype ;
	  $fck->Config['FullPage'] = $isfullpage;
	  if($GLOBALS['cfg_fck_xhtml']=='是'){
	  	$fck->Config['EnableXHTML'] = 'true';
	    $fck->Config['EnableSourceXHTML'] = 'true';
	  }
	  $fck->Value = $fvalue ;
	  if($gtype=="print") $fck->Create();
	  else return $fck->CreateHtml();
  }else{
		require_once(dirname(__FILE__)."/../htmledit/dede_editor.php");
	  $ded = new DedeEditor($fname);
	  $ded->BasePath		= $GLOBALS['cfg_cmspath'].'/include/htmledit/' ;
	  $ded->Width		= '100%' ;
	  $ded->Height		= $nheight ;
	  $ded->ToolbarSet = strtolower($etype);
	  $ded->Value = $fvalue ;
	  if($gtype=="print") $ded->Create();
	  else return $ded->CreateHtml();
	}
}

function SpGetNewInfo(){
	global $cfg_version;
	$nurl = $_SERVER["HTTP_HOST"];
	if( eregi("[a-z\-]{1,}\.[a-z]{2,}",$nurl) ){ $nurl = urlencode($nurl); }
	else{ $nurl = "test"; }
	$gs = "<iframe name='stafrm' src='http://www.dedecms.com/newinfo.php?version=".urlencode($cfg_version)."&formurl=$nurl' frameborder='0' id='stafrm' width='100%' height='50'></iframe>\r\n";
	return $gs;
}
?>
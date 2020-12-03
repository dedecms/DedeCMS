<?php 
//获得一个附加表单
//-----------------------------
function GetFormItem($ctag,$admintype='admin')
{
	$fieldname = $ctag->GetName();
	$formitem = GetSysTemplets("custom_fields_{$admintype}.htm");
	$fieldType = 	$ctag->GetAtt("type");
	$innertext = trim($ctag->GetInnerText());
	if($innertext!=""){
		 if($ctag->GetAtt("type")=='select'){
		 	  $myformItem = '';
		 	  $items = explode(',',$innertext);
		 	  $myformItem = "<select name='$fieldname' style='width:150px'>";
		 	  foreach($items as $v){
		 	 	  $v = trim($v);
		 	 	  if($v!=''){ $myformItem.= "<option value='$v'>$v</option>\r\n"; }
		 	  }
		 	  $myformItem .= "</select>\r\n";
		 	  $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		    $formitem = str_replace("~form~",$myformItem,$formitem);
		    return $formitem;
		 }else if($ctag->GetAtt("type")=='radio'){
		 	  $myformItem = '';
		 	  $items = explode(',',$innertext);
		 	  $i = 0;
		 	  foreach($items as $v){
		 	 	  $v = trim($v);
		 	 	  if($v!=''){
		 	 	  	$myformItem .= ($i==0 ? "<input type='radio' name='$fieldname' class='np' value='$v' checked>$v\r\n" : "<input type='radio' name='$fieldname' class='np' value='$v'>$v\r\n");
		 	 	  	$i++;
		 	 	  }
		 	  }
		 	  $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		    $formitem = str_replace("~form~",$myformItem,$formitem);
		    return $formitem;
		 }
		 else if($ctag->GetAtt("type")=='checkbox'){
		 	  $myformItem = '';
		 	  $items = explode(',',$innertext);
		 	  foreach($items as $v){
		 	 	  $v = trim($v);
		 	 	  if($v!=''){ $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='np' value='$v'>$v\r\n"; }
		 	  }
		 	  $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		    $formitem = str_replace("~form~",$myformItem,$formitem);
		    return $formitem;
		 }
		 else{
		    $formitem = str_replace('~name~',$ctag->GetAtt('itemname'),$formitem);
		    $formitem = str_replace('~form~',$innertext,$formitem);
		    $formitem = str_replace('@value','',$formitem);
		    return $formitem;
		 }
	}
	
	if($fieldType=="htmltext"||$fieldType=="textdata")
	{
		if($admintype=='admin') $innertext = GetEditor($fieldname,'',350,'Basic','string');
		else $innertext = GetEditor($fieldname,'',350,'Member','string');
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="multitext")
	{
		$innertext = "<textarea name='$fieldname' id='$fieldname' style='width:100%;height:80'></textarea>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="datetime")
	{
		$nowtime = GetDateTimeMk(time());
		$innertext = "<input name=\"$fieldname\" value=\"$nowtime\" type=\"text\" id=\"$fieldname\" style=\"width:250px\">";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="img"||$fieldType=="imgfile")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' type='button' class='inputbut' value='浏览...' onClick=\"SelectImage('form1.$fieldname','big')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="media")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' type='button' class='inputbut' value='浏览...' onClick=\"SelectMedia('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="addon")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' type='button' class='inputbut' value='浏览...' onClick=\"SelectSoft('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="media")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' type='button' class='inputbut' value='浏览...' onClick=\"SelectMedia('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($fieldType=="int"||$fieldType=="float")
	{
		$dfvalue = ($ctag->GetAtt('default')!='' ? $ctag->GetAtt('default') : '');
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:100px' value='$dfvalue'> (填写数值)\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else
	{
		$dfvalue = ($ctag->GetAtt('default')!='' ? $ctag->GetAtt('default') : '');
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:250px' value='$dfvalue'>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
}
//---------------------------
//处理不同类型的数据
//---------------------------
function GetFieldValue($dvalue,$dtype,$aid=0,$job='add',$addvar='',$admintype='admin')
{
	global $cfg_basedir,$cfg_cmspath,$adminID,$cfg_ml;
	
	if(!empty($adminID)) $adminid = $adminID;
	else $adminid = $cfg_ml->M_ID;
	
	if($dtype=="int"){
		return GetAlabNum($dvalue);
	}
	else if($dtype=="float"){
	  return GetAlabNum($dvalue);
	}
	else if($dtype=="datetime"){
		return GetMkTime($dvalue);
	}
	else if($dtype=="checkbox"){
		$okvalue = '';
		if(is_array($dvalue)){
			foreach($dvalue as $v){ $okvalue .= ($okvalue=='' ? $v : ",{$v}"); }
		}
		return $okvalue;
	}
	else if($dtype=="textdata")
	{
		if($job=='edit')
		{
			$addvarDirs = explode('/',$addvar);
			$addvarDir = ereg_replace("/".$addvarDirs[count($addvarDirs)-1]."$","",$addvar);
			$mdir = $cfg_basedir.$addvarDir;
			if(!is_dir($mdir)){ MkdirAll($mdir); }
			$fp = fopen($cfg_basedir.$addvar,"w");
		  fwrite($fp,stripslashes($dvalue));
		  fclose($fp);
		  CloseFtp();
	    return $addvar;
	  }else{	
		  $ipath = $cfg_cmspath."/data/textdata";
		  $tpath = ceil($aid/5000);
		  if(!is_dir($cfg_basedir.$ipath)) MkdirAll($cfg_basedir.$ipath,$GLOBALS['cfg_dir_purview']);
		  if(!is_dir($cfg_basedir.$ipath.'/'.$tpath)) MkdirAll($cfg_basedir.$ipath.'/'.$tpath,$GLOBALS['cfg_dir_purview']);
		  $ipath = $ipath.'/'.$tpath;
		  $filename = "{$ipath}/{$aid}.txt";
		  
		  $fp = fopen($cfg_basedir.$filename,"w");
		  fwrite($fp,stripslashes($dvalue));
		  fclose($fp);
		  CloseFtp();
	    return $filename;
	  }
	}
	else if($dtype=="img"||$dtype=="imgfile")
	{
		$iurl = stripslashes($dvalue);
    if(trim($iurl)=="") return "";
    $iurl = trim(str_replace($GLOBALS['cfg_basehost'],"",$iurl));
    $imgurl = "{dede:img text='' width='' height=''} ".$iurl." {/dede:img}";
    if(eregi("^http://",$iurl) && $GLOBALS['cfg_isUrlOpen']) //远程图片
    {
       $reimgs = "";
       if($GLOBALS['cfg_isUrlOpen']){
	       $reimgs = GetRemoteImage($iurl,$adminid);
	       if(is_array($reimgs)){
		        if($dtype=="imgfile") $imgurl = $reimgs[1];
	          else $imgurl = "{dede:img text='' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}";
	       }
	     }else{
	     	  if($dtype=="imgfile") $imgurl = $iurl;
	     	  else $imgurl = "{dede:img text='' width='' height=''} ".$iurl." {/dede:img}";
	     }
    }
    else if($iurl!=""){  //站内图片
	     $imgfile = $cfg_basedir.$iurl;
	     if(is_file($imgfile)){
		      $info = '';
		      $imginfos = GetImageSize($imgfile,$info);
		      if($dtype=="imgfile") $imgurl = $iurl;
		      else $imgurl = "{dede:img text='' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}";
	     }
    }
    return addslashes($imgurl);
	}else{
		return $dvalue;
	}
}
//获得带值的表单(编辑时用)
//-----------------------------
function GetFormItemValue($ctag,$fvalue,$admintype='admin')
{
	global $cfg_basedir;
	$fieldname = $ctag->GetName();
	$formitem = $formitem = GetSysTemplets("custom_fields_{$admintype}.htm"); 
  $innertext = trim($ctag->GetInnerText()); 
  $ftype = $ctag->GetAtt("type");
	$myformItem = '';
	if(eregi("select|radio|checkbox",$ftype)) $items = explode(',',$innertext);
	if($ftype=='select')
	{
		 $myformItem = "<select name='$fieldname' style='width:150px'>";
		 if(is_array($items))
		 {
		    foreach($items as $v){
		 	 	  $v = trim($v);
		 	 	  if($v=='') continue;
		 	 	  $myformItem.= ($fvalue==$v ? "<option value='$v' selected>$v</option>\r\n" : "<option value='$v'>$v</option>\r\n");
		 	  }
		 }
		 $myformItem .= "</select>\r\n";
		 $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		 $formitem = str_replace("~form~",$myformItem,$formitem);
		 return $formitem;
	}else if($ctag->GetAtt("type")=='radio')
	{
		 if(is_array($items))
		 {
		 	  foreach($items as $v)
		 	  {
		 	 	  $v = trim($v);
		 	 	  if($v=='') continue;
		 	 	  $myformItem.= ($fvalue==$v ? "<input type='radio' name='$fieldname' class='np' value='$v' checked>$v\r\n" : "<input type='radio' name='$fieldname' class='np' value='$v'>$v\r\n");
		 	  }
		 }
		 $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		 $formitem = str_replace("~form~",$myformItem,$formitem);
		 return $formitem;
	}
	//checkbox
  else if($ctag->GetAtt("type")=='checkbox')
  {
		 	  $myformItem = '';
		 	  $items = explode(',',$innertext);
		 	  $fvalues = explode(',',$fvalue);
		 	  foreach($items as $v){
		 	 	  $v = trim($v);
		 	 	  if($v=='') continue;
		 	 	  if(in_array($v,$fvalues)){ $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='np' value='$v' checked='checked' />$v\r\n"; }
		 	 	  else{ $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='np' value='$v' />$v\r\n"; }
		 	  }
		 	  $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		    $formitem = str_replace("~form~",$myformItem,$formitem);
		    return $formitem;
  }
	
	//除了以上类型，如果其它的类型自定义了发布表单，则直接输出发布表单优先
	if(!empty($innertext))
	{
		 $formitem = str_replace('~name~',$ctag->GetAtt('itemname'),$formitem);
		 $formitem = str_replace('~form~',$innertext,$formitem);
		 $formitem = str_replace('@value',$fvalue,$formitem);
		 return $formitem;
	}
	
  //文本数据的特殊处理
  if($ftype=="textdata")
  {
  	if(is_file($cfg_basedir.$fvalue)){
  	   $fp = fopen($cfg_basedir.$fvalue,'r');
		   $okfvalue = "";
		   while(!feof($fp)){ $okfvalue .= fgets($fp,1024); }
		   fclose($fp);
	  }else{
	  	$okfvalue = '';
	  }
		
		if($admintype=='admin')  $myformItem = GetEditor($fieldname,$okfvalue,350,'Basic','string')."\r\n <input type='hidden' name='{$fieldname}_file' value='{$fvalue}'>\r\n ";
		else $myformItem = GetEditor($fieldname,$okfvalue,350,'Member','string')."\r\n <input type='hidden' name='{$fieldname}_file' value='{$fvalue}'>\r\n ";
		
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$myformItem,$formitem);
		
		return $formitem;
  }  
	else if($ftype=="htmltext")
	{
		if($admintype=='admin')  $myformItem = GetEditor($fieldname,$fvalue,350,'Basic','string')."\r\n ";
		else $myformItem = GetEditor($fieldname,$fvalue,350,'Member','string')."\r\n ";
		
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$myformItem,$formitem);
		
		return $formitem;
	}
	else if($ftype=="multitext")
	{
		$innertext = "<textarea name='$fieldname' id='$fieldname' style='width:100%;height:80px'>$fvalue</textarea>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="datetime")
	{
		$nowtime = GetDateTimeMk($fvalue);
		$innertext = "<input name=\"$fieldname\" value=\"$nowtime\" type=\"text\" id=\"$fieldname\" style=\"width:250px\">";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="img")
	{
		$ndtp = new DedeTagParse();
    $ndtp->LoadSource($fvalue);
    if(!is_array($ndtp->CTags)){
    	$ndtp->Clear();
    	$fvalue =  "";
    }else
    {
      $ntag = $ndtp->GetTag("img");
      //$fvalue = trim($ntag->GetInnerText());
	  $fvalue = trim($ndtp->InnerText);
    }
		$innertext = "<input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' class='inputbut' type='button' value='浏览...' onClick=\"SelectImage('form1.$fieldname','big')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="imgfile")
	{
		$innertext = "<input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' class='inputbut' type='button' value='浏览...' onClick=\"SelectImage('form1.$fieldname','big')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="media")
	{
		$innertext = "<input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300px'> <input name='".$fieldname."_bt' class='inputbut' type='button' value='浏览...' onClick=\"SelectMedia('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="addon")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' value='$fvalue' style='width:300px'> <input name='".$fieldname."_bt' class='inputbut' type='button' value='浏览...' onClick=\"SelectSoft('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ftype=="int"||$ftype=="float")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:100px' value='$fvalue'> (填写数值)\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:250px' value='$fvalue'>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
}
?>
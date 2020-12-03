<?php 
if (is_array($_GET)){
	foreach($_GET AS $key => $value) if(!isset(${$key})) ${$key} = $value;
}
if (is_array($_POST)){
	if(!$needFilter){
		foreach($_POST AS $key => $value) if(!isset(${$key})) ${$key} = $value;
	}else{
		foreach($_POST AS $key => $value){
	    if(isset(${$key})) continue;
	    if(is_array($value)){
	    	foreach($value as $k=>$v) ${$key}[$k] = $v;
	    	continue;
	    }
	    if(strlen($value)>50){ //禁止字串
	  	   if(!empty($cfg_notallowstr) && eregi($cfg_notallowstr,$value)){
	  		    echo "你的信息中存在非法内容，被系统禁止！<a href='javascript:history.go(-1)'>[返回]</a>"; exit();
	  	   }
	  	   if(!empty($cfg_replacestr)){ //替换字串
	  	  	  $value = eregi_replace($cfg_replacestr,'***',$value);
	  	   }
	    }
	    ${$key} = $value;
    }
	}
}
if (is_array($_COOKIE)){ 
	foreach($_COOKIE AS $key => $value) if(!isset(${$key})) ${$key} = $value;
}
if (is_array($_FILES)) {
  foreach($_FILES AS $name => $value){
     if(!isset(${$name})) ${$name} = $value['tmp_name'];
     foreach($value AS $namen => $valuen){ if(!isset(${$name.'_'.$namen})) ${$name.'_'.$namen} = $valuen; }
  }
}
//$file1_name(原始文件名) $file1_type $file1_tmp_name=$file1 $file1_error $file1_size 文件大小
?>
<?php 
if(is_array($_GET)){
	foreach($_GET AS $key => $value){
		if($cfg_needFilter) FilterNotSafeString($value);
		if(!isset(${$key})) ${$key} = $value;
	}
}

if(is_array($_POST))
{
		foreach($_POST AS $key => $value)
		{
			if(!isset(${$key}))
			{
				if(is_array($value)){
	    	   foreach($value as $nnk=>$nnv){
	    	   	 if($cfg_needFilter) FilterNotSafeString($nnv);
	    	   	 ${$key}[$nnk] = $nnv;
	    	   }
	      }else{
	      	 if($cfg_needFilter) FilterNotSafeString($value);
	      	 ${$key} = $value;
	      }
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

function FilterNotSafeString(&$str)
{
	global $cfg_notallowstr,$cfg_replacestr;
	//禁止字串
	if(strlen($str)>10)
	{
	  	if(!empty($cfg_notallowstr) && eregi($cfg_notallowstr,$str)){
	  		 echo "Messege Error! <a href='javascript:history.go(-1)'>[Go Back]</a>";
	  		 exit();
	  	}
	  	if(!empty($cfg_replacestr)){ //替换字串
	  	  	$str = eregi_replace($cfg_replacestr,'***',$str);
	  	}
	}
}
?>
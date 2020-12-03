<?php 
/*----------------------------------------------
Copyright 2004-2006 by DedeCms.com itprato
Dede Tag模板解析引挚 V4.1 版
最后修改日期：2006-7-4 PHP版本要求：大于或等于php 4.1
---------------------------------------------*/
/****************************************
class DedeTag 标记的数据结构描述
function c____DedeTag(); 
**************************************/

class DedeTag
{
	var $IsReplace=FALSE; //标记是否已被替代，供解析器使用
	var $TagName=""; //标记名称
	var $InnerText=""; //标记之间的文本
	var $StartPos=0; //标记起始位置
	var $EndPos=0; //标记结束位置
	var $CAttribute=""; //标记属性描述,即是class DedeAttribute
	var $TagValue=""; //标记的值
	var $TagID=0;
	
	//获取标记的名称和值
	function GetName(){
		return strtolower($this->TagName);
	}
	function GetValue(){
		return $this->TagValue;
	}
	//下面两个成员函数仅是为了兼容旧版
	function GetTagName(){
		return strtolower($this->TagName);
	}
	function GetTagValue(){
		return $this->TagValue;
	}
  //获取标记的指定属性
	function IsAttribute($str){
       return $this->CAttribute->IsAttribute($str);
	}
	function GetAttribute($str){
    	return $this->CAttribute->GetAtt($str);
	}
	function GetAtt($str){
		return $this->CAttribute->GetAtt($str);
	}
	function GetInnerText(){
		return $this->InnerText;
	}
}

/**********************************************
//DedeTagParse Dede织梦模板类
function c____DedeTagParse();
***********************************************/

class DedeTagParse
{
	var $NameSpace = 'dede'; //标记的名字空间
	var $TagStartWord = '{'; //标记起始
	var $TagEndWord = '}'; //标记结束
	var $TagMaxLen = 64; //标记名称的最大值
	var $CharToLow = TRUE; // TRUE表示对属性和标记名称不区分大小写
	//////////////////////////////////////////////////////
	var $IsCache = FALSE; //是否使用缓冲
	var $TempMkTime = 0;
	var $CacheFile = '';
	/////////////////////////////                       
	var $SourceString = '';//模板字符串
	var $CTags = '';		 //标记集合
	var $Count = -1;		 //$Tags标记个数
	
	function __construct()
 	{
 		if(!isset($GLOBALS['cfg_dede_cache'])) $GLOBALS['cfg_dede_cache'] = '否';
 		if($GLOBALS['cfg_dede_cache']=='是') $this->IsCache = TRUE;
 		else $this->IsCache = FALSE;
 		$this->NameSpace = 'dede';
	  $this->TagStartWord = '{';
	  $this->TagEndWord = '}';
	  $this->TagMaxLen = 64;
	  $this->CharToLow = TRUE;
 		$this->SourceString = '';
 		$this->CTags = Array();
 		$this->Count = -1;
	  $this->TempMkTime = 0;
	  $this->CacheFile = '';
  }
  
  function DedeTagParse(){
  	$this->__construct();
  }
	//设置标记的命名空间，默认为dede
	function SetNameSpace($str,$s="{",$e="}"){
		$this->NameSpace = strtolower($str);
		$this->TagStartWord = $s;
		$this->TagEndWord = $e;
	}
	//重置成员变量或Clear
	function SetDefault(){
		$this->SourceString = '';
		$this->CTags = '';
		$this->Count=-1;
	}
	function GetCount(){
		return $this->Count+1;
  }
	function Clear(){
		$this->SetDefault();
	}
	//检测模板缓冲
	function LoadCache($filename)
	{
		if(!$this->IsCache) return false;
		$cdir = dirname($filename);
		$ckfile = str_replace($cdir,'',$filename).'.cache';
		$ckfullfile = $cdir.''.$ckfile;
		$ckfullfile_t = $cdir.''.$ckfile.'.t';
		$this->CacheFile = $ckfullfile;
		$this->TempMkTime = filemtime($filename);
		if(!file_exists($ckfullfile)||!file_exists($ckfullfile_t)) return false;
		//检测模板最后更新时间
		$fp = fopen($ckfullfile_t,'r');
		$time_info = trim(fgets($fp,64));
		fclose($fp);
		if($time_info != $this->TempMkTime){ return false; }
		//引入缓冲数组
		include($this->CacheFile);
		//把缓冲数组内容读入类
		if(isset($z) && is_array($z)){
			foreach($z as $k=>$v){
				$this->Count++;
				$ctag = new DedeTAg();
				$ctag->CAttribute = new DedeAttribute();
				$ctag->IsReplace = FALSE;
				$ctag->TagName = $v[0];
				$ctag->InnerText = $v[1];
				$ctag->StartPos = $v[2];
				$ctag->EndPos = $v[3];
				$ctag->TagValue = '';
				$ctag->TagID = $k;
				if(isset($v[4]) && is_array($v[4])){
					$i = 0;
					foreach($v[4] as $k=>$v){
						$ctag->CAttribute->Count++;
						$ctag->CAttribute->Items[$k]=$v;
					}
				}
				$this->CTags[$this->Count] = $ctag;
			}
		}
		else{//模板没有缓冲数组
			$this->CTags = '';
	    $this->Count = -1;
		}
		return true;
	}
	//写入缓冲
	function SaveCache()
	{
		$fp = fopen($this->CacheFile.'.t',"w");
		fwrite($fp,$this->TempMkTime."\n");
		fclose($fp);
		$fp = fopen($this->CacheFile,"w");
		flock($fp,3);
		fwrite($fp,'<'.'?php'."\r\n");
		if(is_array($this->CTags)){
			foreach($this->CTags as $tid=>$ctag){
				$arrayValue = 'Array("'.$ctag->TagName.'",';
				$arrayValue .= '"'.str_replace('$','\$',str_replace("\r","\\r",str_replace("\n","\\n",str_replace('"','\"',$ctag->InnerText)))).'"';
				$arrayValue .= ",{$ctag->StartPos},{$ctag->EndPos});";
				fwrite($fp,"\$z[$tid]={$arrayValue}\n");
				if(is_array($ctag->CAttribute->Items)){
					foreach($ctag->CAttribute->Items as $k=>$v){
						$k = trim(str_replace("'","",$k));
						if($k=="") continue;
						if($k!='tagname') fwrite($fp,"\$z[$tid][4]['$k']=\"".str_replace('$','\$',str_replace("\"","\\\"",$v))."\";\n");
					}
				}
			}
		}
		fwrite($fp,"\n".'?'.'>');
		fclose($fp);
	}
	//载入模板文件
	function LoadTemplate($filename){
		$this->SetDefault();
		$fp = @fopen($filename,"r") or die("DedeTag Engine Load Template \"$filename\" False！");
		while($line = fgets($fp,1024))
			$this->SourceString .= $line;
		fclose($fp);
		if($this->LoadCache($filename)) return;
		else $this->ParseTemplet();
	}
	function LoadTemplet($filename){
		$this->LoadTemplate($filename);
	}
	function LoadFile($filename){
		$this->LoadTemplate($filename);
	}
	//载入模板字符串
	function LoadSource($str){
		$this->SetDefault();
		$this->SourceString = $str;
		$this->IsCache = FALSE;
		$this->ParseTemplet();
	}
	function LoadString($str){
		$this->LoadSource($str);
	}
	//获得指定名称的Tag的ID(如果有多个同名的Tag,则取没有被取代为内容的第一个Tag)
	function GetTagID($str){
		if($this->Count==-1) return -1;
		if($this->CharToLow) $str=strtolower($str);
		foreach($this->CTags as $ID=>$CTag){
			if($CTag->TagName==$str && !$CTag->IsReplace){
				return $ID;
				break;
			}
		}
		return -1;
	}
	//获得指定名称的CTag数据类(如果有多个同名的Tag,则取没有被分配内容的第一个Tag)
	function GetTag($str){
		if($this->Count==-1) return "";
		if($this->CharToLow) $str=strtolower($str);
		foreach($this->CTags as $ID=>$CTag){
			if($CTag->TagName==$str && !$CTag->IsReplace){
				return $CTag;
				break;
			}
		}
		return "";
	}
	function GetTagByName($str)
	{ return $this->GetTag($str); }
	//获得指定ID的CTag数据类
	function GetTagByID($ID){
		if(isset($this->CTags[$ID])) return $this->CTags[$ID];
	  else return "";
	}
	//
	//分配指定ID的标记的值
	//
	function Assign($tagid,$str)
	{
		if(isset($this->CTags[$tagid]))
		{
			$this->CTags[$tagid]->IsReplace = TRUE;
			if( $this->CTags[$tagid]->GetAtt("function")!="" ){
				$this->CTags[$tagid]->TagValue = $this->EvalFunc(
					$str,
					$this->CTags[$tagid]->GetAtt("function") 
				);
			}
			else 
		  { $this->CTags[$tagid]->TagValue = $str; }
		}
	}
	//分配指定名称的标记的值，如果标记包含属性，请不要用此函数
	function AssignName($tagname,$str)
	{
		foreach($this->CTags as $ID=>$CTag){
			if($CTag->TagName==$tagname) $this->Assign($ID,$str);
		}
	}
	//处理特殊标记
	function AssignSysTag()
	{
		for($i=0;$i<=$this->Count;$i++)
		{
		  $CTag = $this->CTags[$i];
		  //获取一个外部变量
		  if( $CTag->TagName == "global" ){
				 $this->CTags[$i]->IsReplace = TRUE;
				 $this->CTags[$i]->TagValue = $this->GetGlobals($CTag->GetAtt("name"));
				 if( $this->CTags[$i]->GetAtt("function")!="" ){
					$this->CTags[$i]->TagValue = $this->EvalFunc(
						$this->CTags[$i]->TagValue,$this->CTags[$i]->GetAtt("function") 
					);
				}
		  }
		  //引入静态文件
			else if( $CTag->TagName == "include" ){
				$this->CTags[$i]->IsReplace = TRUE;
				$this->CTags[$i]->TagValue = $this->IncludeFile($CTag->GetAtt("file"),$CTag->GetAtt("ismake"));
			}
			//循环一个普通数组
			else if( $CTag->TagName == "foreach" )
			{
				$rstr = "";
				$arr = $this->CTags[$i]->GetAtt("array");
				if(isset($GLOBALS[$arr]))
				{
					foreach($GLOBALS[$arr] as $k=>$v){
						$istr = "";
						$istr .= str_replace("[field:key/]",$k,$this->CTags[$i]->InnerText);
						$rstr .= str_replace("[field:value/]",$v,$istr);
					}
				}
				$this->CTags[$i]->IsReplace = TRUE;
				$this->CTags[$i]->TagValue = $rstr;
			}
			//运行PHP接口
		  if( $CTag->GetAtt("runphp") == "yes" )
		  {
			  $DedeMeValue = "";
			  if($CTag->GetAtt("source")=='value')
			  { $runphp = $this->CTags[$i]->TagValue; }
			  else{
			  	$DedeMeValue = $this->CTags[$i]->TagValue;
			  	$runphp = $CTag->GetInnerText();
			  }
			  $runphp = eregi_replace("'@me'|\"@me\"|@me",'$DedeMeValue',$runphp);
			  eval($runphp.";");
			  $this->CTags[$i]->IsReplace = TRUE;
			  $this->CTags[$i]->TagValue = $DedeMeValue;
	    }
    }
	}
	//把分析模板输出到一个字符串中
	//不替换没被处理的值
	function GetResultNP()
	{
		$ResultString = "";
		if($this->Count==-1){
			return $this->SourceString;
		}
		$this->AssignSysTag();
		$nextTagEnd = 0;
		$strok = "";
		for($i=0;$i<=$this->Count;$i++){
			if($this->CTags[$i]->GetValue()!=""){
			  if($this->CTags[$i]->GetValue()=='#@Delete@#') $this->CTags[$i]->TagValue = "";
			  $ResultString .= substr($this->SourceString,$nextTagEnd,$this->CTags[$i]->StartPos-$nextTagEnd);
			  $ResultString .= $this->CTags[$i]->GetValue();
			  $nextTagEnd = $this->CTags[$i]->EndPos;
		  }
		}
		$slen = strlen($this->SourceString);
		if($slen>$nextTagEnd){
		   $ResultString .= substr($this->SourceString,$nextTagEnd,$slen-$nextTagEnd);
	  }
		return $ResultString;
	}
	//把分析模板输出到一个字符串中,并返回
	function GetResult()
	{
		$ResultString = "";
		if($this->Count==-1){
			return $this->SourceString;
		}
		$this->AssignSysTag();
		$nextTagEnd = 0;
		$strok = "";
		for($i=0;$i<=$this->Count;$i++){
			$ResultString .= substr($this->SourceString,$nextTagEnd,$this->CTags[$i]->StartPos-$nextTagEnd);
			$ResultString .= $this->CTags[$i]->GetValue();
			$nextTagEnd = $this->CTags[$i]->EndPos;
		}
		$slen = strlen($this->SourceString);
		if($slen>$nextTagEnd){
		   $ResultString .= substr($this->SourceString,$nextTagEnd,$slen-$nextTagEnd);
	  }
		return $ResultString;
	}
	//直接输出分析模板
	function Display()
	{
		echo $this->GetResult();
	}
	//把分析模板输出为文件
	function SaveTo($filename)
	{
		$fp = @fopen($filename,"w") or die("DedeTag Engine Create File False：$filename");
		fwrite($fp,$this->GetResult());
		fclose($fp);
	}
	//解析模板
	function ParseTemplet()
	{
		$TagStartWord = $this->TagStartWord;
		$TagEndWord = $this->TagEndWord;
		$sPos = 0; $ePos = 0;
		$FullTagStartWord =  $TagStartWord.$this->NameSpace.":";
		$sTagEndWord =  $TagStartWord."/".$this->NameSpace.":";
		$eTagEndWord = "/".$TagEndWord;
		$tsLen = strlen($FullTagStartWord);
		$sourceLen=strlen($this->SourceString);
		if( $sourceLen <= ($tsLen + 3) ) return;
		$cAtt = new DedeAttributeParse();
		$cAtt->CharToLow = $this->CharToLow;
		//遍历模板字符串，请取标记及其属性信息
		for($i=0;$i<$sourceLen;$i++)
		{
			$tTagName = "";
			$sPos = strpos($this->SourceString,$FullTagStartWord,$i);
			$isTag = $sPos;
			if($i==0){
				$headerTag = substr($this->SourceString,0,strlen($FullTagStartWord));
				if($headerTag==$FullTagStartWord){ $isTag=TRUE; $sPos=0; }
			}
			if($isTag===FALSE) break;
 			if($sPos > ($sourceLen-$tsLen-3) ) break;
			
			for($j=($sPos+$tsLen);$j<($sPos+$tsLen+$this->TagMaxLen);$j++)
			{
				if($j>($sourceLen-1)) break;
				else if(ereg("[ \t\r\n]",$this->SourceString[$j])
					||$this->SourceString[$j] == $this->TagEndWord) break;
				else $tTagName .= $this->SourceString[$j];
			}
			if(strtolower($tTagName)=="comments")
			{
				$endPos = strpos($this->SourceString,$sTagEndWord ."comments",$i);
				if($endPos!==false) $i=$endPos+strlen($sTagEndWord)+8;
				continue;
			}
			$i = $sPos+$tsLen;
			$sPos = $i;
			$fullTagEndWord = $sTagEndWord.$tTagName;
			$endTagPos1 = strpos($this->SourceString,$eTagEndWord,$i);
			$endTagPos2 = strpos($this->SourceString,$fullTagEndWord,$i);
			$newStartPos = strpos($this->SourceString,$FullTagStartWord,$i);
			if($endTagPos1===FALSE) $endTagPos1=0;
			if($endTagPos2===FALSE) $endTagPos2=0;
			if($newStartPos===FALSE) $newStartPos=0;
			//判断用何种标记作为结束
			if($endTagPos1>0 && 
			  ($endTagPos1 < $newStartPos || $newStartPos==0) && 
			  ($endTagPos1 < $endTagPos2 || $endTagPos2==0 ))
			{
				$ePos = $endTagPos1;
				$i = $ePos + 2;
			}
			else if($endTagPos2>0){
				$ePos = $endTagPos2;
				$i = $ePos + strlen($fullTagEndWord)+1;
			}
			else{
				echo "Parse error the tag ".($this->GetCount()+1)." $tTagName' is incorrect !<br/>";
			}
			//分析所找到的标记位置等信息
			$attStr = "";
			$innerText = "";
			$startInner = 0;
			for($j=$sPos;$j < $ePos;$j++)
			{
				if($startInner==0 && $this->SourceString[$j]==$TagEndWord)
				{ $startInner=1; continue; }
				if($startInner==0) $attStr .= $this->SourceString[$j];
				else $innerText .= $this->SourceString[$j];
			}
			$cAtt->SetSource($attStr);
			if($cAtt->CAttribute->GetTagName()!="")
			{
				$this->Count++;
				$CDTag = new DedeTag();
				$CDTag->TagName = $cAtt->CAttribute->GetTagName();
				$CDTag->StartPos = $sPos - $tsLen;
				$CDTag->EndPos = $i;
				$CDTag->CAttribute = $cAtt->CAttribute;
				$CDTag->IsReplace = FALSE;
				$CDTag->TagID = $this->Count;
				$CDTag->InnerText = $innerText;
				$this->CTags[$this->Count] = $CDTag;
				//定义函数或执行PHP语句
				if( $CDTag->TagName == "define"){
				  @eval($CDTag->InnerText);
			  }
			}	
		}//结束遍历模板字符串
		if($this->IsCache) $this->SaveCache();
	}
	//处理某字段的函数
	function EvalFunc($fieldvalue,$functionname)
	{
		$DedeFieldValue = $fieldvalue;
		$functionname = str_replace("{\"","[\"",$functionname);
		$functionname = str_replace("\"}","\"]",$functionname);
		$functionname = eregi_replace("'@me'|\"@me\"|@me",'$DedeFieldValue',$functionname);
		$functionname = "\$DedeFieldValue = ".$functionname;
		eval($functionname.";");
		if(empty($DedeFieldValue)) return "";
		else return $DedeFieldValue;
	}
	//获得一个外部变量
	function GetGlobals($varname)
	{
		$varname = trim($varname);
		//禁止在模板文件读取数据库密码
		if($varname=="dbuserpwd"||$varname=="cfg_dbpwd") return "";
		//正常情况
		if(isset($GLOBALS[$varname])) return $GLOBALS[$varname];
		else return "";
	}
	//引入文件
	function IncludeFile($filename,$ismake='no')
	{
		global $cfg_df_style;
		$restr = "";
		if(file_exists($filename)){ $okfile = $filename; }
		else if( file_exists(dirname(__FILE__)."/".$filename) ){ $okfile = dirname(__FILE__)."/".$filename; }
		else if( file_exists(dirname(__FILE__)."/../".$filename) ){ $okfile = dirname(__FILE__)."/../".$filename; }
		else if( file_exists(dirname(__FILE__)."/../templets/".$filename) ){ $okfile = dirname(__FILE__)."/../templets/".$filename; }
		else if( file_exists(dirname(__FILE__)."/../templets/".$cfg_df_style."/".$filename) ){ $okfile = dirname(__FILE__)."/../templets/".$cfg_df_style."/".$filename; }
		else{ return "无法在这个位置找到： $filename"; }
		//编译
  	if($ismake=="yes"){
  		require_once(dirname(__FILE__)."/inc_arcpart_view.php");
  		$pvCopy = new PartView();
  		$pvCopy->SetTemplet($okfile,"file");
  		$restr = $pvCopy->GetResult();
    }else{
  	  $fp = @fopen($okfile,"r");
		  while($line=fgets($fp,1024)) $restr.=$line;
		  fclose($fp);
	  }
		return $restr;
	}
}

/**********************************************
//class DedeAttribute Dede模板标记属性集合
function c____DedeAttribute();
**********************************************/
//属性的数据描述
class DedeAttribute
{
     var $Count = -1;
     var $Items = ""; //属性元素的集合
     //获得某个属性
     function GetAtt($str){
       if($str=="") return "";
       if(isset($this->Items[$str])) return $this->Items[$str];
       else return "";
     }
     //同上
     function GetAttribute($str){
       return $this->GetAtt($str);
     }
     //判断属性是否存在
     function IsAttribute($str){
       if(isset($this->Items[$str])) return true;
       else return false;
     }
     //获得标记名称
     function GetTagName(){
       return $this->GetAtt("tagname");
     }
     // 获得属性个数
     function GetCount(){
			return $this->Count+1;
	   }
}
/*******************************
//属性解析器
function c____DedeAttributeParse();
********************************/
class DedeAttributeParse
{
	var $SourceString = "";
	var $SourceMaxSize = 1024;
	var $CAttribute = ""; //属性的数据描述类
	var $CharToLow = TRUE;
	//////设置属性解析器源字符串////////////////////////
	function SetSource($str="")
	{
    $this->CAttribute = new DedeAttribute();
		//////////////////////
		$strLen = 0;
		$this->SourceString = trim(preg_replace("/[ \t\r\n]{1,}/"," ",$str));
		$strLen = strlen($this->SourceString);
		if($strLen>0&&$strLen<=$this->SourceMaxSize){
			$this->ParseAttribute();
		}
	}
	//////解析属性(私有成员，仅给SetSource调用)/////////////////
	function ParseAttribute()
	{
		$d = "";
		$tmpatt="";
		$tmpvalue="";
		$startdd=-1;
		$ddtag="";
		$notAttribute=true;
		$strLen = strlen($this->SourceString);
		// 这里是获得Tag的名称,可视情况是否需要
		// 如果不在这个里解析,则在解析整个Tag时解析
		// 属性中不应该存在tagname这个名称
		for($i=0;$i<$strLen;$i++)
		{
			$d = substr($this->SourceString,$i,1);
			if($d==' '){
				$this->CAttribute->Count++;
				if($this->CharToLow) $this->CAttribute->Items["tagname"]=strtolower(trim($tmpvalue));
				else $this->CAttribute->Items["tagname"]=trim($tmpvalue);
				$tmpvalue = "";
				$notAttribute = false;
				break;
			}
			else
				$tmpvalue .= $d;
		}
		//不存在属性列表的情况
		if($notAttribute)
		{
			$this->CAttribute->Count++;
			if($this->CharToLow) $this->CAttribute->Items["tagname"]=strtolower(trim($tmpvalue));
			else $this->CAttribute->Items["tagname"]=trim($tmpvalue);
		}
		//如果字符串含有属性值，遍历源字符串,并获得各属性
		if(!$notAttribute){
		for($i;$i<$strLen;$i++)
		{
			$d = substr($this->SourceString,$i,1);
			if($startdd==-1){
				if($d!="=")	$tmpatt .= $d;
				else{
					if($this->CharToLow) $tmpatt = strtolower(trim($tmpatt));
					else $tmpatt = trim($tmpatt);
					$startdd=0;
				}
			}
			else if($startdd==0)
			{
				switch($d){
					case ' ':
						continue;
						break;
					case '\'':
						$ddtag='\'';
						$startdd=1;
						break;
					case '"':
						$ddtag='"';
						$startdd=1;
						break;
					default:
						$tmpvalue.=$d;
						$ddtag=' ';
						$startdd=1;
						break;
				}
			}
			else if($startdd==1)
			{
				if($d==$ddtag){
					$this->CAttribute->Count++;
          $this->CAttribute->Items[$tmpatt] = trim($tmpvalue);//strtolower(trim($tmpvalue));
					$tmpatt = "";
					$tmpvalue = "";
					$startdd=-1;
				}
				else
					$tmpvalue.=$d;
			}
		}
		if($tmpatt!=""){
			$this->CAttribute->Count++;
			$this->CAttribute->Items[$tmpatt]=trim($tmpvalue);//strtolower(trim($tmpvalue));
		}
		//完成属性解析
	}//for
	}//has Attribute
}
?>
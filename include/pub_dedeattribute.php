<?php 
//本文件是从pub_dedetag.php分离出来的属性解析器
/**********************************************
//class DedeAtt Dede模板标记属性集合
function c____DedeAtt();
**********************************************/
//属性的数据描述
class DedeAtt
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
function c____DedeAttParse();
********************************/
class DedeAttParse
{
	var $SourceString = "";
	var $SourceMaxSize = 1024;
	var $CAtt = ""; //属性的数据描述类
	var $CharToLow = TRUE;
	//////设置属性解析器源字符串////////////////////////
	function SetSource($str="")
	{
    $this->CAtt = new DedeAtt();
		//////////////////////
		$strLen = 0;
		$this->SourceString = trim(preg_replace("/[ \t\r\n]{1,}/"," ",$str));
		$strLen = strlen($this->SourceString);
		if($strLen>0&&$strLen<=$this->SourceMaxSize){
			$this->ParseAtt();
		}
	}
	//////解析属性(私有成员，仅给SetSource调用)/////////////////
	function ParseAtt()
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
				$this->CAtt->Count++;
				if($this->CharToLow) $this->CAtt->Items["tagname"]=strtolower(trim($tmpvalue));
				else $this->CAtt->Items["tagname"]=trim($tmpvalue);
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
			$this->CAtt->Count++;
			if($this->CharToLow) $this->CAtt->Items["tagname"]=strtolower(trim($tmpvalue));
			else $this->CAtt->Items["tagname"]=trim($tmpvalue);
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
					$this->CAtt->Count++;
          $this->CAtt->Items[$tmpatt] = trim($tmpvalue);//strtolower(trim($tmpvalue));
					$tmpatt = "";
					$tmpvalue = "";
					$startdd=-1;
				}
				else
					$tmpvalue.=$d;
			}
		}
		if($tmpatt!=""){
			$this->CAtt->Count++;
			$this->CAtt->Items[$tmpatt]=trim($tmpvalue);//strtolower(trim($tmpvalue));
		}
		//完成属性解析
	}//for
	}//has Attribute
}
?>
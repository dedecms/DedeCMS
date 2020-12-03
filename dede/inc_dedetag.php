<?
//////////////////////////////////////////////////
//Dede Tag模板解析引挚 V1.1 版
//最后修改日期：2005-1-10
//PHP版本要求：大于4.0
//本文件包含:
//class DedeTag 标记的数据描述
//class DedeAttribute Dede模板标记属性集合的数据描述
//class DedeTagParse Dede模板分析器
//class DedeAttributeParse Dede模板标记属性分析器

//Dede编织梦幻之旅
/////////////////////////////////////////////////

/**********************************************
class DedeTag 仅作Tag标记的数据结构描述
***********************************************/

class DedeTag
{
	var $IsReplace; //标记是否已被替代，供解析器使用
	var $TagName; //标记名称
	var $InnerText; //标记之间的文本
	var $StartPos; //标记起始位置
	var $EndPos; //标记结束位置
	var $CAttribute; //标记属性描述,即是class DedeAttribute
	function DedeTag()
	{
		$this->IsReplace=FALSE;
		$this->TagName="";
		$this->InnerText="";
		$this->StartPos=0;
		$this->EndPos=0;
		$this->CAttribute=new DedeAttribute();
	}
	function GetTagName()
	{
		return strtolower($this->TagName);
	}
    //
    //以下成员与CAttribute的同名成员功能一致
    //
	function IsAttribute($str)
	{
       return $this->CAttribute->IsAttribute($str);
	}
	function GetAttribute($str)
	{
    	return $this->CAttribute->GetAtt($str);
	}
	function GetAtt($str)
	{
		return $this->CAttribute->GetAtt($str);
	}
}

/**********************************************
//DedeTagParse Dede模板分析器
//Dede模板标记格式：
<dede:tagname name="value">InnerText</dede>
<dede:tagname name="value"></dede>
<dede:tagname name="value"/>
//语法与XML语法相同，但不支持标记嵌套
//dede是织梦内容管理系统V2.0版所使用的命名空间
//如果你进行你自己的开发时使用别的命名空间，请在载入模板之前调用：
DedeTagParse->SetNameSpace($namespace);来设置(允许用中文)
//命名空间仅是为了区别于HTML,考虑性能原因,本版的解析器不能解析同一个模板的多个命名空间。
//官方网址：www.dedecms.com
***********************************************/

class DedeTagParse
{
	var $SourceString = "";//模板字符串
	var $SourceStringCopy  ="";//模板字符串

	var $SourceLen=0;	 //模板字符串长度

	var $CTags="";		 //$Tags标记集合
	var $CTagsCopy = "";

	var $Count=-1;		 //$Tags标记个数

	var $NameSpace="dede"; //标记的命名空间

	var $TagStartWord = "<"; //标记起始

	var $TagEndWord = ">"; //标记结束

    //
    //
    //
    function ResetSource()
    {
    	$this->SourceString = $this->SourceStringCopy;
		$this->CTags = $this->CTagsCopy;
    }
	//
	//获得指定名称的Tag的ID(如果有多个同名的Tag,则取没有被取代为内容的第一个Tag)
	//
	function GetTagID($str)
	{
		if($this->CTags=="") return -1;
		$str = strtolower($str);
		foreach($this->CTags as $ID=>$CTag)
		{
			if($CTag->TagName==$str && $CTag->IsReplace==FALSE)
			{
				return $ID;
				break;
			}
		}
		return -1;
	}
	//
	//获得指定名称的Tag(如果有多个同名的Tag,则取没有被取代为内容的第一个Tag)
	//
	function GetTag($str)
	{
		if($this->CTags=="") return "";
		$str = strtolower($str);
		foreach($this->CTags as $ID=>$CTag)
		{
			if($CTag->TagName==$str && $CTag->IsReplace==FALSE)
			{
				return $CTag;
				break;
			}
		}
		return "";
	}
	//
	//把指定的Tag取代为指定的字符串
	//
	function ReplaceTag($tagid,$str)
	{
		 $slen = strlen($str);
		 $rlen = 0;
		 $moveLen = 0;
         //异常情况
         if($tagid==-1) return FALSE;
		 if(!isset($this->CTags[$tagid])) return FALSE;
		 if($this->CTags[$tagid]->IsReplace) return FALSE;
         /////////////////////////////////////////////////
		 $rlen = $this->CTags[$tagid]->EndPos - $this->CTags[$tagid]->StartPos;
		 $moveLen = $slen-$rlen;
		 $this->SourceString = &substr_replace($this->SourceString,$str,$this->CTags[$tagid]->StartPos,$rlen);
		 $this->CTags[$tagid]->IsReplace=TRUE;
		 $this->SourceLen+=$moveLen;
		 for($i=0;$i<=$this->Count;$i++)
		 {
		 	if($i!=$tagid)
		 	{
		 		if($this->CTags[$i]->StartPos > $this->CTags[$tagid]->EndPos)
		 		{
		 			$this->CTags[$i]->StartPos+=$moveLen;
		 			$this->CTags[$i]->EndPos+=$moveLen;
		 		}
		 	}
		 }
         return TRUE;
	}
	//
	//获得经过处理后的模板字符串
	//
	function GetResult()
	{
		return $this->SourceString;
	}
	//
	//把经过处理后的模板保存为其它文件
	//
	function SaveTo($filename)
	{
		$fp = @fopen($filename,"w") or die("DedeTag解析器无法创建文件：$filename");
		fwrite($fp,$this->SourceString);
		fclose($fp);
	}
	//
	//设置标记的命名空间，默认为dede
	//
	function SetNameSpace($str)
	{
		$this->NameSpace = strtolower($str);
	}
	//
	//重置成员变量（在打开模板前均调用这个方法，以免被追加使用）
	//
	function SetDefault()
	{
		$this->SourceString="";
		$this->SourceLen=0;
		$this->CTag= new DedeTag();
		$this->Count=-1;
	}
	//
	//打开模板文件
	//
	function LoadTemplate($filename)
	{
		$this->SetDefault();
		$fp = @fopen($filename,"r") or die("DedeTag解析器无法读取文件：$filename");
		while($line = fgets($fp,1024))
			$this->SourceString .= $line;
		fclose($fp);
		$this->SourceLen=strlen($this->SourceString);
		$this->ParseSource();
	}
	//
	//载入模板字符串（注意：这里用引用调用）
	//
	function LoadSource($str)
	{
		$this->SetDefault();
		$this->SourceString = $str;
		$this->SourceLen=strlen($this->SourceString);
		$this->ParseSource();
	}
	//
	//获得Tag的总数
	//
	function GetCount()
	{
		return $this->Count+1;
	}
	//
	//核心解析器，仅作私有成员
	//
	function ParseSource()
	{
		$d = "";
		$startPos = 0;
		$endTagPos1 = 0;
		$endTagPos2 = 0;
		$endPos = 0;
		$tag = "";
		$TagStartWord = $this->TagStartWord;
		$TagEndWord = $this->TagEndWord;
		$nLen = strlen($this->NameSpace);
		if($this->SourceString=="") return;
		////把SourceString备份一个副本
		$this->SourceStringCopy = $this->SourceString;
		$CDAttribute = new DedeAttributeParse();
		$atFirst = 0;
		for($i=0;$i<$this->SourceLen;$i++)
		{
			$startPos = 0;
			$endTagPos1 = 0;
			$endTagPos2 = 0;
			$endPos = 0;
			$tag = "";
			$att = "";
			//寻找标记
			$startPos = strpos($this->SourceString,$TagStartWord.$this->NameSpace,$i);
			//修正第一个字符为TagStartWord的情况
			if($i==0)
			{
				if($this->SourceString[0]==$TagStartWord)
				{
					$startPos=TRUE;
					$atFirst=1;
				}
			}
			//如果找不到任何标记，退出循环
			if($startPos==FALSE) break;
			else
			{
				if($startPos==1) $i=1;
				else $i=$startPos+1;
				//检测标记以/NameSpace结束还是/EndTag结束
				//这里的逻辑容易产生bug，并这种检测模式也较浪费
				//解决方法
				//1、是多次调用重一模板时用ResetSource()恢复原来分析到的标记和源模板
				//2、可考虑让这个函数先检测 endtag 然后确定前一个是否为 /  的方法,这样可以避免不可预见的查找
				$endTagPos1 = strpos($this->SourceString,"/".$TagEndWord,$i);
				$endTagPos2 = strpos($this->SourceString,$TagStartWord."/".$this->NameSpace,$i);
				if($endTagPos1==FALSE) $endTagPos1=0;
				if($endTagPos2==FALSE) $endTagPos2=0;
				if(($endTagPos2<$endTagPos1&&$endTagPos2!=0)||$endTagPos1==0)
					$endPos = strpos($this->SourceString,$TagEndWord,$endTagPos2)+1;
				else
					$endPos=$endTagPos1+2;
				if($startPos==1)
				{
					$startPos = $startPos-1;
				}
			}
	        //处理检测到的标记
			if($endPos-$startPos>$nLen+4)
			{
				$tag = substr($this->SourceString,$startPos,$endPos-$startPos);
				$i=$endPos;
				$endPost = 0;
				$endPost = strpos($tag,"/".$TagEndWord,0);
				if($endPost==FALSE) $endPost = strpos($tag,$TagEndWord,0);
				$att = substr($tag,$nLen+2,$endPost-$nLen-2);
				if($att!=FALSE)
				{
					if(ereg("[$TagEndWord$TagStartWord]",$att)) continue;
					$CDAttribute->SetSource($att);
					if($CDAttribute->CAttribute->GetTagName()!="")
					{
						$this->Count++;
						$CDTag = new DedeTag();
						$CDTag->TagName = $CDAttribute->CAttribute->GetTagName();
						$CDTag->InnerText = $this->GetInnerText($tag);
						$CDTag->StartPos = $startPos;
						$CDTag->EndPos = $endPos;
						$CDTag->CAttribute = $CDAttribute->CAttribute;
						$CDTag->IsReplace = FALSE;
					    $this->CTags[$this->Count] = $CDTag;
					}
					
				}//结束$att!=FALSE
				
			}//结束处理检测到的标记
			
		}//结束遍历模板文件
		//把CTags备份一个副本
		$this->CTagsCopy = $this->CTags;
	}
	//
	//GetInnerText仅作私有成员
	//
	function GetInnerText(&$str)
	{
		$startPos = 0;
		$endPos = 0;
		$innertext = "";
		if(ereg("/".$this->TagEndWord,$str)) return "";
		else
		{
			$startPos = strpos($str,$this->TagEndWord,0);
			$endPos = strpos($str,$this->TagStartWord."/".$this->NameSpace,$startPos);
			$innertext = substr($str,$startPos+1,$endPos-$startPos-1);
			if($innertext==FALSE) return "";
			else return $innertext;
		}
	}
}

/**********************************************
//class DedeAttribute Dede模板标记属性集合
**********************************************/
//属性的数据描述
class DedeAttribute
{
       var $Count = -1;
       var $Items = ""; //属性元素的集合
       //
       //获得某个属性
       //
       function GetAtt($str)
       {
       		$str = strtolower(trim($str));
       		if($str=="") return "";
       		if(isset($this->Items[$str])) return $this->Items[$str];
            else return "";
       }
       //同上
       function GetAttribute($str)
       {
       		return $this->GetAtt($str);
       }
       //
       //判断属性是否存在
       //
       function IsAttribute($str)
       {
       		if(!empty($this->Items[$str])) return true;
            else return false;
       }
       //
       //获得标记名称
       //
       function GetTagName()
       {
         return $this->GetAtt("tagname");
       }
       //
       // 获得属性个数
       //
       function GetCount()
	   {
			return $this->Count+1;
	   }
}
//
//属性解析器
//
class DedeAttributeParse
{
	var $SourceString = "";
	var $SourceMaxSize = 1024;
	var $CAttribute = ""; //属性的数据描述类
	//////设置属性解析器源字符串////////////////////////
	function SetSource($str="")
	{
        $this->CAttribute = new DedeAttribute();
		//////////////////////
		$strLen = 0;
		$this->SourceString = trim(ereg_replace("[ \t\r\n]{1,}"," ",$str));
		$strLen = strlen($this->SourceString);
		if($strLen>0&&$strLen<=$this->SourceMaxSize)
		{
			//仅当源字符非空和不长于最大限定值时才解析属性
			$this->parSource();
		}
	}
	//////解析属性(私有成员，仅给SetSource调用)/////////////////
	function ParSource()
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
			if($d==' ')
			{
				$this->CAttribute->Count++;
				$this->CAttribute->Items["tagname"]=strtolower(trim($tmpvalue));
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
			$this->CAttribute->Items["tagname"]=strtolower(trim($tmpvalue));
		}
		//如果字符串含有属性值，遍历源字符串,并获得各属性
		if(!$notAttribute){
		for($i;$i<$strLen;$i++)
		{
			$d = substr($this->SourceString,$i,1);
			if($startdd==-1)
			{
				if($d!="=")	$tmpatt .= $d;
				else
				{
					$tmpatt = strtolower(trim($tmpatt));
					$startdd=0;
				}
			}
			else if($startdd==0)
			{
				switch($d)
				{
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
				if($d==$ddtag)
				{
					$this->CAttribute->Count++;
                    $this->CAttribute->Items[$tmpatt]=strtolower(trim($tmpvalue));
					$tmpatt = "";
					$tmpvalue = "";
					$startdd=-1;
				}
				else
					$tmpvalue.=$d;
			}
		}
		if($tmpatt!="")
		{
			$this->CAttribute->Count++;
			$this->CAttribute->Items[$tmpatt]=strtolower(trim($tmpvalue));
		}
		//完成属性解析
	}//for
	}//has Attribute
}
?>
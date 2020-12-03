<?
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/inc_channel_unit_functions.php");
//-------------------------------------
//class TypeLink
//获得文章的位置和文章的类目位置等
//凡以Logic开头的成员,一般在内调用,不在外调用(相当于保护级成员)
//-------------------------------------
class TypeLink
{
	var $typeDir;
	var $dsql;
	var $TypeID;
	var $baseDir;
	var $modDir;
	var $indexUrl;
	var $indexName;
	var $TypeInfos;
	var $SplitSymbol;
	var $valuePosition;
	var $valuePositionName;
	var $OptionArrayList;
	//构造函数///////
	//-------------
	//php5构造函数
	//-------------
	function __construct($typeid)
 	{
		$this->indexUrl = $GLOBALS['cfg_basehost'].$GLOBALS['cfg_indexurl'];
		$this->indexName = $GLOBALS['cfg_indexname'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->modDir = $GLOBALS['cfg_templets_dir'];
		$this->SplitSymbol = $GLOBALS['cfg_list_symbol'];
		$this->dsql = new DedeSql(false);
		$this->TypeID = $typeid;
	  $this->valuePosition = "";
	  $this->valuePositionName = "";
	  $this->typeDir = "";
	  $this->OptionArrayList = "";
		//载入类目信息
		$query = "
		Select #@__arctype.*,#@__channeltype.typename as ctypename 
		From #@__arctype left join #@__channeltype 
		on #@__channeltype.ID=#@__arctype.channeltype 
		where #@__arctype.ID='$typeid'
		";
		if($typeid > 0){
		  $this->dsql->SetQuery($query);
		  $this->TypeInfos = $this->dsql->GetOne();
		  //模板和路径变量处理
 		  $this->TypeInfos['typedir'] = MfTypedir($this->TypeInfos['typedir']);
 		  $this->TypeInfos['tempindex'] = MfTemplet($this->TypeInfos['tempindex']);
 		  $this->TypeInfos['templist'] = MfTemplet($this->TypeInfos['templist']);
 		  $this->TypeInfos['temparticle'] = MfTemplet($this->TypeInfos['temparticle']);
 		  $this->TypeInfos['tempone'] = MfTemplet($this->TypeInfos['tempone']);
	  }
  }
	//对于使用默认构造函数的情况
	//GetPositionLink()将不可用
	function TypeLink($typeid){
		$this->__construct($typeid);
	}
	//关闭数据库连接，析放资源
	//-----------------------
	function Close(){
		$this->dsql->Close();
	}
	//------------------------
	//重设类目ID
	//------------------------
	function SetTypeID($typeid){
		$this->TypeID = $typeid;
	  $this->valuePosition = "";
	  $this->valuePositionName = "";
	  $this->typeDir = "";
	  $this->OptionArrayList = "";
		//载入类目信息
		$query = "
		Select #@__arctype.*,#@__channeltype.typename as ctypename 
		From #@__arctype left join #@__channeltype 
		on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID='$typeid' ";
		$this->dsql->SetQuery($query);
		$this->TypeInfos = $this->dsql->GetOne();
	}
	//-----------------------
	//获得这个类目的路径
	//-----------------------
	function GetTypeDir(){
		if(empty($this->TypeInfos['typedir'])) return $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		else return $this->TypeInfos['typedir'];
	}
	//-----------------------------
	//获得文章网址
	//----------------------------
	function GetFileUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0,$siterefer="",$sitepath=""){
		$articleRule = "";
		$articleDir = "";
		if($namerule!="") $articleRule = $namerule;
		else if(is_array($this->TypeInfos)) $articleRule = $this->TypeInfos['namerule'];
		if($artdir!="") $articleDir = $artdir;
		else if(is_array($this->TypeInfos)) $articleDir = $this->GetTypeDir();
		return GetFileUrl($aid,$typeid,$timetag,$title,$ismake,$rank,$articleRule,$articleDir,$money,$siterefer,$sitepath);
	}
	//获得新文件网址
	//本函数会自动创建目录
	function GetFileNewName($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0,$siterefer="",$sitepath=""){
		$articleRule = "";
		$articleDir = "";
		if($namerule!="") $articleRule = $namerule;
		else if(is_array($this->TypeInfos)) $articleRule = $this->TypeInfos['namerule'];
		if($artdir!="") $articleDir = $artdir;
		else if(is_array($this->TypeInfos)) $articleDir = $this->GetTypeDir();
		return GetFileNewName($aid,$typeid,$timetag,$title,$ismake,$rank,$articleRule,$articleDir,$money,$siterefer,$sitepath);
	}
	//----------------------------------------------
	//获得某类目的链接列表 如：类目一>>类目二>> 这样的形式
	//islink 表示返回的列表是否带连接
	//----------------------------------------------
	function GetPositionLink($islink=true)
	{
		$indexpage = "<a href='".$this->indexUrl."'>".$this->indexName."</a>";
		if($this->valuePosition!="" && $islink){
			return $this->valuePosition;
		}
		else if($this->valuePositionName!="" && !$islink){
			return $this->valuePositionName;
		}
		else if($this->TypeID==0){
			if($islink) return $indexpage;
			else return "没指定分类！";
		}
		else
		{
			if($islink)
			{
			  $this->valuePosition = $this->GetOneTypeLink($this->TypeInfos);
			  if($this->TypeInfos['reID']!=0){ //调用递归逻辑
			  	$this->LogicGetPosition($this->TypeInfos['reID'],true);
			  }
			  $this->valuePosition = $indexpage.$this->SplitSymbol.$this->valuePosition;
			  return $this->valuePosition.$this->SplitSymbol;
		  }else{
		  	$this->valuePositionName = $this->TypeInfos['typename'];
			  if($this->TypeInfos['reID']!=0){ //调用递归逻辑
			    $this->LogicGetPosition($this->TypeInfos['reID'],false);
			  }
			  return $this->valuePositionName;
		  }
		}
	}
	//获得名字列表
	function GetPositionName(){
		return $this->GetPositionLink(false);
	}
	//获得某类目的链接列表，递归逻辑部分
	function LogicGetPosition($ID,$islink)
	{	
		$this->dsql->SetQuery("Select ID,reID,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl From #@__arctype where ID='".$ID."'");
		$tinfos = $this->dsql->GetOne();
		
		if($islink) $this->valuePosition = $this->GetOneTypeLink($tinfos).$this->SplitSymbol.$this->valuePosition; 
		else $this->valuePositionName = $tinfos['typename'].$this->SplitSymbol.$this->valuePositionName; 
		
		if($tinfos['reID']>0) $this->LogicGetPosition($tinfos['reID'],$islink);
		else return 0;
		
	}
	//获得某个类目的超链接信息
	//-----------------------
	function GetOneTypeLink($typeinfos){
	  $typepage = $this->GetOneTypeUrl($typeinfos);
		$typelink = "<a href='".$typepage."'>".$typeinfos['typename']."</a>";
		return $typelink;
	}
	//获得某分类连接的URL
	//---------------------
	function GetOneTypeUrl($typeinfos){
		return GetTypeUrl($typeinfos['ID'],MfTypedir($typeinfos['typedir']),$typeinfos['isdefault'],
		$typeinfos['defaultname'],$typeinfos['ispart'],$typeinfos['namerule2'],$typeinfos['siteurl']);
	}
	//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
	//-------------------------------------------
	function GetSunID($typeid=-1,$tb="#@__archives",$channel=0){
    $ids = TypeGetSunID($typeid,$this->dsql,$tb,$channel);
		return $ids;
	}
	//------------------------------
	//获得类别列表
	//hid 是指默认选中类目，0 表示“请选择类目”或“不限类目”
	//oper 是用户允许管理的类目，0 表示所有类目
	//channeltype 是指类目的内容类型，0 表示不限频道
	//--------------------------------
	function GetOptionArray($hid=0,$oper=0,$channeltype=0,$usersg=0){
		return $this->GetOptionList($hid,$oper,$channeltype,$usersg);
  }
	function GetOptionList($hid=0,$oper=0,$channeltype=0,$usersg=0)
	{
    if(!$this->dsql) $this->dsql = new DedeSql();
    $this->OptionArrayList = "";
    if($hid>0){
    	$row = $this->dsql->GetOne("Select ID,typename,ispart From #@__arctype where ID='$hid'");
    	if($row['ispart']==1) $this->OptionArrayList .= "<option value='".$row['ID']."' style='background-color:#DFDFDB;color:#888888' selected>".$row['typename']."</option>\r\n";
      else $this->OptionArrayList .= "<option value='".$row['ID']."' selected>".$row['typename']."</option>\r\n";
    }
    if($channeltype==0) $ctsql="";
    else $ctsql=" And channeltype='$channeltype' ";
    	
    if($oper!=0){ $query = "Select ID,typename,ispart From #@__arctype where ispart<>2 And ID='$oper' $ctsql"; }
    else{ $query = "Select ID,typename,ispart From #@__arctype where ispart<>2 And reID=0 $ctsql order by sortrank asc"; }
      
    $this->dsql->SetQuery($query);
    $this->dsql->Execute();
    	
     while($row=$this->dsql->GetObject()){
        if($row->ID!=$hid){
          if($row->ispart==1) $this->OptionArrayList .= "<option value='".$row->ID."' style='background-color:#EFEFEF;color:#666666'>".$row->typename."</option>\r\n";
          else $this->OptionArrayList .= "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
        }
        $this->LogicGetOptionArray($row->ID,"─");
     }
     return $this->OptionArrayList; 
	}
	function LogicGetOptionArray($ID,$step){
		$this->dsql->SetQuery("Select ID,typename,ispart From #@__arctype where reID='".$ID."' And ispart<>2 order by sortrank asc");
		$this->dsql->Execute($ID);
		while($row=$this->dsql->GetObject($ID)){
      if($row->ispart==1) $this->OptionArrayList .= "<option value='".$row->ID."' style='background-color:#EFEFEF;color:#666666'>$step".$row->typename."</option>\r\n";
      else $this->OptionArrayList .= "<option value='".$row->ID."'>$step".$row->typename."</option>\r\n";
      $this->LogicGetOptionArray($row->ID,$step."─");
    }
	}
	//----------------------------
	//获得与该类相关的类目，本函数应用于模板标记{dede:channel}{/dede:channel}中
	//$typetype 的值为： sun 下级分类 self 同级分类 top 顶级分类
	//-----------------------------
	function GetChannelList($typeid=0,$reID=0,$row=8,$typetype='sun',$innertext='',
	$col=1,$tablewidth=100,$myinnertext='')
	{
		if($typeid==0) $typeid = $this->TypeID;
		if($row=="") $row = 8;
		if($reID=="") $reID = 0;
		if($col=="") $col = 1;
		
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col = 1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		
		if($typetype=="") $typetype="sun";
		if($innertext=="") $innertext = GetSysTemplets("channel_list.htm");
		
		if($reID==0 && $typeid>0){
		  $dbrow = $this->dsql->GetOne("Select reID From #@__arctype where ID='$typeid' ");
		  if(is_array($dbrow)) $reID = $dbrow['reID'];
		}
		
		$likeType = "";
		if($typetype=="top"){
		  $sql = "Select ID,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl 
		  From #@__arctype where reID=0 And ishidden<>1 order by sortrank asc limit 0,$row";
		}
		else if($typetype=="sun"||$typetype=="son"){
		  $sql = "Select ID,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl 
		  From #@__arctype where reID='$typeid' And ishidden<>1 order by sortrank asc limit 0,$row";
		}
		else if($typetype=="self"){
			$sql = "Select ID,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl 
			From #@__arctype where reID='$reID' And ishidden<>1 order by sortrank asc limit 0,$row";
		}
		//And ID<>'$typeid'
	  $dtp2 = new DedeTagParse();
	  $dtp2->SetNameSpace("field","[","]");
	  $dtp2->LoadSource($innertext);
    $this->dsql->SetQuery($sql);
	  $this->dsql->Execute();
	  $line = $row;
	  $GLOBALS['autoindex'] = 0;
	  if($col>1) $likeType = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
		for($i=0;$i<$line;$i++)
		{
       if($col>1) $likeType .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $likeType .= "	<td width='$colWidth'>\r\n";
         if($row=$this->dsql->GetArray())
         {
			     //处理同级栏目中，当前栏目的样式
			     if($typetype=="self" && $row['ID']=="$typeid"){
			        if($myinnertext!=''){
			        	 $linkOkstr = $myinnertext;
			        	 $row['typelink'] = $this->GetOneTypeUrl($row);
			        	 $linkOkstr = str_replace("~typelink~",$row['typelink'],$linkOkstr);
			        	 $linkOkstr = str_replace("~typename~",$row['typename'],$linkOkstr);
			        	 $likeType .= $linkOkstr;
			        }
			     }else{//非当前栏目
			       $row['typelink'] = $this->GetOneTypeUrl($row);
			       if(is_array($dtp2->CTags)){
			     	   foreach($dtp2->CTags as $tagid=>$ctag)
			     	   { if(isset($row[$ctag->GetName()])) $dtp2->Assign($tagid,$row[$ctag->GetName()]); }
			       }
			       $likeType .= $dtp2->GetResult();
			     }
         }
         if($col>1) $likeType .= "	</td>\r\n";
         $GLOBALS['autoindex']++;
       }//Loop Col
       if($col>1) $i += $col - 1;
       if($col>1) $likeType .= "	</tr>\r\n";
    }//Loop for $i
		if($col>1) $likeType .= "	</table>\r\n";
    $this->dsql->FreeResult();
		return $likeType;
	}//GetChannel
}//End Class
?>
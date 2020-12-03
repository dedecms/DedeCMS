<?
//class TypeUnit
//这个类主要是封装频道管理时的一些复杂操作 
//--------------------------------
$CanRmDir = false; //由于删除目录可能会有不可意料的问题，因此禁止删除目录
require_once(dirname(__FILE__)."/../include/config_base.php");
class TypeUnit
{
	var $dsql;
	var $artDir;
	var $baseDir;
	var $idCounter;
	var $idArrary;
	var $shortName;
	
	//-------------
	//php5构造函数
	//-------------
	function __construct()
 	{
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = 0;
  }
	function TypeUnit()
	{
		$this->__construct();
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		if(is_object($this->dsql)){
			$this->dsql->Close();
			@$this->dsql=0;
		}
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($chennel=0,$nowdir=0)
	{
		if($this->dsql==0){ $this->dsql = new DedeSql(); }
		
		if($chennel>0)
		{	$this->dsql->SetQuery("Select ID,typedir,typename,ispart From #@__arctype where ID='$chennel'");}
		else
		{	$this->dsql->SetQuery("Select ID,typedir,typename,ispart From #@__arctype where reID=0 order by sortrank"); }
		$this->dsql->Execute(0);
		
		while($row=$this->dsql->GetObject(0))
		{	 
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2'>\r\n";
			
			//普通列表和带封面的频道
			if($ispart==0||$ispart==1)
			{
			  echo "  <tr bgcolor='#F5F5F5'>\r\n";
			  echo "  <td width='2%' class='bline'><img style='cursor:hand' onClick=\"showHide('suns".$ID."');\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
			  echo "  <td class='bline'><a href='catalog_do.php?cid=".$ID."&dopost=listArchives' oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\">".$typeName."[ID:".$ID."]</a>";
			  echo "  </td></tr>\r\n";
			}
			//独立页面
			else if($ispart==2)
			{
				 echo "  <tr height='24' bgcolor='#F5F5F5'>\r\n";
			   echo "  <td width='2%' class='bline'><img style='cursor:hand' onClick=\"showHide('suns".$ID."');\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
			   echo "  <td class='bline'><a href='catalog_edit.php?ID=".$ID."' oncontextmenu=\"SingleMenu(this,$ID,'".urlencode($typeName)."')\">".$typeName."[ID:".$ID."]</a>";
			   echo "  </td></tr>\r\n";
			}
			
			echo "  <tr id='suns".$ID."'><td colspan='2'>\r\n";
			echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
			
			if($nowdir == -1) $this->LogicListAllSunType($ID,"　");
			else if($nowdir == $ID) $this->LogicListAllSunType($ID,"　");
			
			echo "    </table>\r\n</td></tr>\r\n";
			echo "</table>\r\n";
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step)
	{
		$fid = $ID;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart From #@__arctype where reID='".$ID."' order by sortrank");
		$this->dsql->Execute($fid);
		if($this->dsql->GetTotalRow($fid)>0)
		{
		  while($row=$this->dsql->GetObject($fid))
		  {
			  $typeDir = $row->typedir;
			  $typeName = $row->typename;
			  $reID = $row->reID;
			  $ID = $row->ID;
			  $ispart = $row->ispart;
			  if($step=="　") $stepdd = 2;
			  else $stepdd = 3;
			  
			  //普通列表和带封面的频道
			  if($ispart==0||$ispart==1)
			  {
			     echo "    <tr height='24' oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\">\r\n";
			     echo "    <td><a href='catalog_do.php?cid=".$ID."&dopost=listArchives'>$step ·".$typeName."[ID:".$ID."]</a>";
			     echo "    </td></tr>\r\n";
			  }
			  //独立页面
			  else if($ispart==2)
			  {
				  echo "    <tr height='24' oncontextmenu=\"SingleMenu(this,$ID,'".urlencode($typeName)."')\">\r\n";
			    echo "    <td><a href='catalog_do.php?cid=".$ID."&dopost=listArchives'>$step ·".$typeName."[ID:".$ID."]</a>";
			    echo "    </td></tr>\r\n";
			  }
			  
			  $this->LogicListAllSunType($ID,$step."　");
		  }
		}
	}
	//------------------------------------------------------
	//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
	//------------------------------------------------------
	function GetSunTypes($ID,$channel=0)
	{
		if(!is_object($this->dsql)) $this->dsql = new DedeSql(false);
		$this->idArray[$this->idCounter]=$ID;
		$this->idCounter++;
		$fid = $ID;
	  if($channel!=0) $csql = " And channeltype=$channel ";
	  else $csql = "";
		$this->dsql->SetQuery("Select ID From #@__arctype where reID=$ID $csql");
		$this->dsql->Execute("gs".$fid);
    //if($this->dsql->GetTotalRow("gs".$fid)!=0)
		//{
		while($row=$this->dsql->GetObject("gs".$fid)){
			$nid = $row->ID;
			$this->GetSunTypes($nid,$channel);
		}
		//}
		return $this->idArray;
	}
	//----------------------------------------------------------------------------
	//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
	//----------------------------------------------------------------------------
	function GetSunID($ID,$tb="#@__archives",$channel=0)
	{
		$this->sunID = "";
		if($this->dsql==0) $this->dsql = new DedeSql(false);
		$this->idCounter = 0;
		$this->idArray = "";
		$this->GetSunTypes($ID,$channel);
		$this->dsql->Close();
		$this->dsql = 0;
		$rquery = "";
		for($i=0;$i<$this->idCounter;$i++)
		{
			if($i!=0) $rquery .= " Or ".$tb.".typeid='".$this->idArray[$i]."' ";
			else      $rquery .= "    ".$tb.".typeid='".$this->idArray[$i]."' ";
		}
		reset($this->idArray);
		$this->idCounter = 0;
		return " (".$rquery.") ";
	}
	//----------------------------------------
	//删除类目
	//----------------------------------------
	function DelType($ID,$isDelFile)
	{
		
		$this->idCounter = 0;
		$this->idArray = "";
		$this->GetSunTypes($ID);
		
		$query = "
		Select #@__arctype.*,#@__channeltype.typename as ctypename,
		#@__channeltype.addtable 
		From #@__arctype left join #@__channeltype 
		on #@__channeltype.ID=#@__arctype.channeltype 
		where #@__arctype.ID='$ID'
		";
		$typeinfos = $this->dsql->GetOne($query);
		if(!is_array($typeinfos)) return false;
		$indir = $typeinfos['typedir'];
		$addtable = $typeinfos['addtable'];
		$ispart = $typeinfos['ispart'];
		$defaultname = $typeinfos['defaultname'];
		
		//删除数据库里的相关记录
		foreach($this->idArray as $id){
			$myrow = $this->dsql->GetOne("Select * From #@__arctype where ID='$id'");
			//删除目录和目录里的所有文件
		  if($isDelFile=="yes"){
			  if($myrow['typedir']!="" && $myrow['typedir']!="/" 
			  && is_dir($this->baseDir.$myrow['typedir']))
			  {
			    $this->RmDirFile($this->baseDir.$myrow['typedir']);
		      if(is_dir($this->baseDir.$myrow['typedir']) && $GLOBALS['CanRmDir'])
		      { @rmdir($this->baseDir.$myrow['typedir']); }
		    }
		  }
			//删除单独页面
		  if($myrow['ispart']==2 && $myrow['typedir']==""){
			  if( is_file($this->baseDir."/".$myrow['defaultname']) )
			  { @unlink($this->baseDir."/".$myrow['defaultname']); }
		  }
			//删除数据库信息
			$this->dsql->SetQuery("Delete From #@__arctype where ID='$id'");
			$this->dsql->ExecuteNoneQuery();
			$this->dsql->SetQuery("Delete From #@__archives where typeid='$id'");
			$this->dsql->ExecuteNoneQuery();
			$this->dsql->SetQuery("update #@__archives set typeid2=0 where typeid2='$id'");
			$this->dsql->ExecuteNoneQuery();
			$this->dsql->SetQuery("Delete From #@__spec where typeid='$id'");
			$this->dsql->ExecuteNoneQuery();
			$this->dsql->SetQuery("Delete From #@__feedback where typeid='$id'");
			$this->dsql->ExecuteNoneQuery();
      if($addtable!=""){
        $this->dsql->SetQuery("Delete From $addtable where typeid='$id'");
			  $this->dsql->ExecuteNoneQuery();
		  }
		}
		
		//删除目录和目录里的所有文件
		if($isDelFile=="yes"){
			if($indir!="" && $indir!="/" && is_dir($this->baseDir.$indir)){
			  $this->RmDirFile($this->baseDir.$indir);
		    if(is_dir($this->baseDir.$indir) && $GLOBALS['CanRmDir'])
		    { @rmdir($this->baseDir.$indir); }
		  }
		}
		
		//删除单独页面
		if($ispart==2 && $indir==""){
			if( is_file($this->baseDir."/".$defaultname) ) @unlink($this->baseDir."/".$defaultname);
		}
		
		@reset($this->idArray);
		$this->idCounter = 0;
		
		return true;
	}
	//---------------------------
	//---- 删除指定目录的所有文件
	//---------------------------
	function RmDirFile($indir)
	{
   		if(!is_dir($indir)) return;
   		$dh = dir($indir);
   		while($file = $dh->read()) {
      	if($file == "." || $file == "..") continue;
      	else if(is_file("$indir/$file")) unlink("$indir/$file");
     	 	else{
         		$this->RmDirFile("$indir/$file");
      	}
      	if(is_dir("$indir/$file") && $GLOBALS['CanRmDir']){
         	@rmdir("$indir/$file");
      	}
   		}
   		$dh->close();
   		return(1);
	}
}
?>
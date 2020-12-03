<?php 
//class TypeTree
//目录树(用于选择栏目)
//--------------------------------
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php");

class TypeTreeMember
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
		$this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = 0;
  }
	function TypeTreeMember()
	{
		$this->__construct();
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		if($this->dsql){
			@$this->dsql->Close();
		}
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($nowdir=0,$issend=-1,$opall=false,$channelid=0)
	{
		if(!is_object($this->dsql)){ $this->dsql = new DedeSql(false); }

		$this->dsql->SetQuery("Select ID,typedir,typename,ispart,channeltype,issend From #@__arctype where reID=0 order by sortrank");
		$this->dsql->Execute(0);
		$lastID = GetCookie('lastCidTree');
		while($row=$this->dsql->GetObject(0))
		{
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			$dcid = $row->channeltype;
			$dissend = $row->issend;
			if($ispart>=2||TestHasChannel($ID,$channelid,$issend)==0) continue;
			if($ispart==0 || ($ispart==1 && $opall))
			{//普通列表
				if(($channelid==0 || $channelid==$dcid) 
				&& ($issend!=1 || $dissend==1))
				{
					$smenu = " <input type='checkbox' name='selid' id='selid$ID' class='np' onClick=\"ReSel($ID,'$typeName')\"> ";
				}else{
					$smenu = "[×]";
				}
			}else if($ispart==1)
			{//带封面的频道
				$smenu = "[封面]";
			}
			echo "<dl class='topcc'>\n";
			echo "<dd><img style='cursor:hand' onClick=\"LoadSuns('suns{$ID}',{$ID},{$channelid});\" src='img/tree_explode.gif' width='11' height='11'> $typeName{$smenu}</dd>\n";
			echo "</dl>\n";
			echo "<div id='suns".$ID."' class='sunct'>";
			if($lastID==$ID){
			   $this->LogicListAllSunType($ID,"　　",$opall,$issend,$channelid);
			}
			echo "</div>\r\n";
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step,$opall,$issend,$channelid,$nums=0)
	{
		$fid = $ID;

		if($nums){
			$step = '　　'.$step;
		}
		$nums = 1;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart,channeltype,issend From #@__arctype where reID='".$ID."' order by sortrank");
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
			  $dcid = $row->channeltype;
        $dissend = $row->issend;

			  if($ispart>=2||TestHasChannel($ID,$channelid,$issend)==0) continue;

			  //普通列表
			  if(($ispart==0 || ($ispart==1 && $opall)) 
			  && ($issend!=1 || $dissend==1))
			  {
			  	if($channelid==0 || $channelid==$dcid) $smenu = " <input type='checkbox' name='selid' id='selid$ID' class='np' onClick=\"ReSel($ID,'$typeName')\"> ";
			  	else $smenu = "[×]";
			  	$timg = " <img src='img/tree_list.gif'> ";
			  }
			  //带封面的频道
			  else if($ispart==1){
			  	$timg = " <img src='img/tree_part.gif'> ";
			  	$smenu = "[封面]";
			  }
			  echo '<dl class="topcc">'."\n";
			  echo '<dd>'.$step.$typeName."{$smenu}</dd>\n";
			  echo "</dl>\n";
			  $this->LogicListAllSunType($ID,$step,$opall,$issend,$channelid, $nums);
		  }
		}
	}
}
?>
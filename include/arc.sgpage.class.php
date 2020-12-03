<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/arc.partview.class.php");

class sgpage
{
	var $dsql;
	var $dtp;
	var $TypeID;
	var $Fields;
	var $TypeLink;
	var $partView;

	//php5构造函数
	function __construct($aid)
	{
		global $cfg_basedir,$cfg_templets_dir,$cfg_df_style,$envs;

		$this->dsql = $GLOBALS['dsql'];
		$this->dtp = new DedeTagParse();
		$this->dtp->refObj = $this;
		$this->dtp->SetNameSpace("dede","{","}");
		$this->Fields = $this->dsql->GetOne("Select * From `#@__sgpage` where aid='$aid' ");
		$envs['aid'] = $this->Fields['aid'];

		//设置一些全局参数的值
		foreach($GLOBALS['PubFields'] as $k=>$v)
		{
			$this->Fields[$k] = $v;
		}
		if($this->Fields['ismake']==1)
		{
			$pv = new PartView();
			$pv->SetTemplet($this->Fields['body'],'string');
			$this->Fields['body'] = $pv->GetResult();
		}
		$tplfile = $cfg_basedir.str_replace('{style}',$cfg_templets_dir.'/'.$cfg_df_style,$this->Fields['template']);
		$this->dtp->LoadTemplate($tplfile);
		$this->ParseTemplet();
	}

	//php4构造函数
	function sgpage($aid)
	{
		$this->__construct($aid);
	}

	//显示内容
	function Display()
	{
		$this->dtp->Display();
	}

	//获取内容
	function GetResult()
	{
		return $this->dtp->GetResult();
	}

	//保存结果为文件
	function SaveToHtml()
	{
		$filename = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_cmspath'].'/'.$this->Fields['filename'];
		$filename = ereg_replace('/{1,}','/',$filename);
		$this->dtp->SaveTo($filename);
	}

	//解析模板里的标签
	function ParseTemplet()
	{
		$GLOBALS['envs']['likeid'] = $this->Fields['likeid'];
		MakeOneTag($this->dtp,$this);
	}

	//关闭所占用的资源
	function Close()
	{
	}
}//End Class
?>
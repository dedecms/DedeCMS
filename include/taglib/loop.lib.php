<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC.'/dedevote.class.php');
function lib_loop(&$ctag,&$refObj)
{
	global $dsql;
	$attlist="table|,tablename|,row|8,sort|,if|,ifcase|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$innertext = trim($ctag->GetInnertext());
	$revalue = '';
	if(!empty($table)) $tablename = $table;

	if($tablename==''||$innertext=='') return '';
	if($if!='') $ifcase = $if;

	if($sort!='') $sort = " order by $sort desc ";
	if($ifcase!='') $ifcase=" where $ifcase ";
	$dsql->SetQuery("Select * From $tablename $ifcase $sort limit 0,$row");
	$dsql->Execute();
	$ctp = new DedeTagParse();
	$ctp->SetNameSpace("field","[","]");
	$ctp->LoadSource($innertext);
	$GLOBALS['autoindex'] = 0;
	while($row = $dsql->GetArray())
	{
		$GLOBALS['autoindex']++;
		foreach($ctp->CTags as $tagid=>$ctag)
		{
				if($ctag->GetName()=='array')
				{
						$ctp->Assign($tagid, $row);
				}
				else
				{
					if( !empty($row[$ctag->GetName()])) $ctp->Assign($tagid,$row[$ctag->GetName()]); 
				}
		}
		$revalue .= $ctp->GetResult();
	}
	return $revalue;
}
?>
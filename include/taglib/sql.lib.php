<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
function lib_sql(&$ctag,&$refObj)
{
	global $dsql,$sqlCt;
	$attlist="sql|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	//传递环境参数
	preg_match_all("/~([A-Za-z0-9]+)~/s", $sql, $conditions);
	if(is_array($conditions))
	{
		foreach ($conditions[1] as $key => $value)
		{
			if(isset($refObj->Fields[$value]))
			{
				$sql = str_replace($conditions[0][$key], "'".addslashes($refObj->Fields[$value])."'", $sql);
			}
		}
	}

	$revalue = '';
	$Innertext = trim($ctag->GetInnerText());

	if($sql=='' || $Innertext=='') return '';
	if(empty($sqlCt)) $sqlCt = 0;

	$ctp = new DedeTagParse();
	$ctp->SetNameSpace('field','[',']');
	$ctp->LoadSource($Innertext);

	$thisrs = 'sq'.$sqlCt;
	$dsql->Execute($thisrs,$sql);
	$GLOBALS['autoindex'] = 0;
	while($row = $dsql->GetArray($thisrs))
	{
		$sqlCt++;
		$GLOBALS['autoindex']++;
		foreach($ctp->CTags as $tagid=>$ctag)
		{
			  if($ctag->GetName()=='array')
				{
						$ctp->Assign($tagid,$row);
				}
				else
				{
					if( !empty($row[$ctag->GetName()])) { 
						$ctp->Assign($tagid,$row[$ctag->GetName()]); 
				  }else{ 
				  	$ctp->Assign($tagid,""); 
				  }
				}
		}
		$revalue .= $ctp->GetResult();
	}
	return $revalue;
}
?>
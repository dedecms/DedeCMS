<?
function GetRemoteImg(&$body)
{
	
}
///////////////////////////////////////////
function big52gb(&$Text){
	$fp = fopen("../php/gbtable/big5-gb.db", "r");
	$max=strlen($Text)-1;
	for($i=0;$i<$max;$i++)
	{
		$h=ord($Text[$i]);
		if($h>0x80)
		{
			$l=ord($Text[$i+1]);
			if($h==161 && $l==64)
			{
				$gb="°°"; 
			}
			else
			{
				fseek($fp,($h-160)*510+($l-1)*2); 
				$gb=fread($fp,2); 
			}
			$Text[$i]=$gb[0];
			$Text[$i+1]=$gb[1];
			$i++;
		}
	}
	fclose($fp);
	return $Text;
}
//////////////////////////////////////////////
function gb2big5(&$Text)   
{  
	$fp = fopen("../php/gbtable/gb-big5.db", "r");  
	$max=strlen($Text)-1;  
	for($i=0;$i<$max;$i++)  
	{  
		$h=ord($Text[$i]); 
		if($h>0x80)  
		{  
			$l=ord($Text[$i+1]);  
			if($h==161 && $l==64)  
			{ $gb=" "; }  
			else 
			{
				fseek($fp,($h-160)*510+($l-1)*2);
				$gb=fread($fp,2);
			}  
			$Text[$i]=$gb[0];  
			$Text[$i+1]=$gb[1];
			$i++;  
		}  
	} 
	fclose($fp);
	return $Text;
}
$ll = "ƒ„ « ≤√¥»À£ø";
echo $ll." <br>";
gb2big5(&$ll);
echo $ll." <br>";
?>
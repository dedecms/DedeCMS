<?php 
class DedeEditor
{
	var $InstanceName ;
	var $BasePath ;
	var $Width ;
	var $Height ;
	var $ToolbarSet ;
	var $Value ;

	// PHP 5 Constructor (by Marcus Bointon <coolbru@users.sourceforge.net>)
	function __construct( $instanceName )
 	{
		$this->InstanceName	= $instanceName ;
		$this->BasePath		= '' ;
		$this->Width		= '100%' ;
		$this->Height		= '300' ;
		$this->ToolbarSet	= '1' ;
		$this->Value		= '' ;
	}
	
	// PHP 4 Contructor
	function DedeEditor( $instanceName )
	{
		$this->__construct( $instanceName ) ;
	}

	function Create()
	{
		echo $this->CreateHtml() ;
	}
	
	function CreateHtml()
	{
		$HtmlValue = htmlspecialchars( $this->Value ) ;

		$Html = '<div>' ;
		
		if($this->IsCompatible())
		{
			$File = 'index.php' ;

			$Link = "{$this->BasePath}{$File}?modetype=".$this->ToolbarSet."&InstanceName={$this->InstanceName}&height=".$this->Height."&width=".$this->Width ;
			
			$fname = $this->InstanceName;
			$Html = "";
			
			$Html .= "<input type=\"hidden\" id=\"{$this->InstanceName}\" name=\"{$this->InstanceName}\" value=\"{$HtmlValue}\" style=\"display:none\" />" ;
			
			$Html .= "<iframe id=\"{$this->InstanceName}_frame\" src=\"{$Link}\" width=\"{$this->Width}\" height=\"{$this->Height}\" frameborder=\"no\" scrolling=\"no\"></iframe>" ;
		
		}
		else
		{
			if ( strpos( $this->Width, '%' ) === false )
				$WidthCSS = $this->Width . 'px' ;
			else
				$WidthCSS = $this->Width ;

			if ( strpos( $this->Height, '%' ) === false )
				$HeightCSS = $this->Height . 'px' ;
			else
				$HeightCSS = $this->Height ;

			$Html .= "<textarea name=\"{$this->InstanceName}\" rows=\"4\" cols=\"40\" style=\"width: {$WidthCSS}; height: {$HeightCSS}\">{$HtmlValue}</textarea>" ;
		}

		$Html .= '</div>' ;
		
		return $Html ;
	}

	function IsCompatible()
	{
		global $HTTP_USER_AGENT ;

		if ( isset( $HTTP_USER_AGENT ) )
			$sAgent = $HTTP_USER_AGENT ;
		else
			$sAgent = $_SERVER['HTTP_USER_AGENT'] ;

		if ( strpos($sAgent, 'MSIE') !== false && strpos($sAgent, 'mac') === false && strpos($sAgent, 'Opera') === false )
		{
			$iVersion = (float)substr($sAgent, strpos($sAgent, 'MSIE') + 5, 3) ;
			return ($iVersion >= 5.5) ;
		}
		else if ( strpos($sAgent, 'Gecko/') !== false )
		{
			$iVersion = (int)substr($sAgent, strpos($sAgent, 'Gecko/') + 6, 8) ;
			return ($iVersion >= 20030210) ;
		}
		else
			return false ;
	}

}

?>
<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: templets.php,v 1.1 2009/08/04 04:07:32 blt Exp $
 */
if(!defined('DEDEINC') || !isset($_GROUPS)) exit("403 Forbidden!");
$_templets = DEDEGROUP.'/templets/'.$_GROUPS['theme'];
$_GROUPS['theme'] = !is_dir($_templets) ? 'default' : $_GROUPS['theme'];
define('GROUP_TPL', str_replace('\\','/',DEDEROOT).'/group/templets/'.$_GROUPS['theme']); 
?>
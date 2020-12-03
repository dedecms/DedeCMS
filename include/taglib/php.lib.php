<?php
if (!defined('DEDEINC'))
    exit('Request Error!');

function lib_php(&$ctag, &$refObj)
{
    global $dsql;
    global $db;
    $phpcode = trim($ctag->GetInnerText());
    if ($phpcode == '')
        return '';
    ob_start();
    extract($GLOBALS, EXTR_SKIP);
    @eval($phpcode);
    $revalue = ob_get_contents();
    ob_clean();
    return $revalue;
}
?>
<?php
if(!defined('DEDEINC')) exit("Request Error!");

function __autoload($classname)
{
    global $cfg_soft_lang;
    $classname = preg_replace("/[^0-9a-z_]/i", '', $classname);
    if( class_exists ( $classname ) )
    {
        return TRUE;
    }
    $classfile = $classname.'.php';
    $libclassfile = $classname.'.class.php';
    if ( is_file ( DEDEINC.'/'.$libclassfile ) )
    {
        require DEDEINC.'/'.$libclassfile;
    }
    else if( is_file ( DEDEMODEL.'/'.$classfile ) )
    {
        require DEDEMODEL.'/'.$classfile;
    }
    else
    {
        if (DEBUG_LEVEL === TRUE)
        {
            echo '<pre>';
            echo $classname.'类找不到';
            echo '</pre>';
            exit ();
        }
        else
        {
            header ( "location:/404.html" );
            die ();
        }
    }
}
?>
<?
if(!empty($_GET))   { foreach($_GET AS $key => $value) $$key = addslashes($value); }
if(!empty($_POST))  { foreach($_POST AS $key => $value) $$key = addslashes($value); }
if(!empty($_COOKIE)){ foreach($_COOKIE AS $key => $value) $$key = addslashes($value); }
if(!empty($_FILES)) {
   foreach($_FILES AS $name => $value){
      $$name = addslashes($value['tmp_name']);
      foreach($value AS $namen => $valuen){
        ${$name.'_'.$namen} = addslashes($value[$namen]);
      }
   }
}
?>
<?
if(is_array($_GET))   { foreach($_GET AS $key => $value) $$key = addslashes($value); }
if(is_array($_POST))  { foreach($_POST AS $key => $value) $$key = addslashes($value); }
if(is_array($_COOKIE)){ foreach($_COOKIE AS $key => $value) $$key = addslashes($value); }
if(is_array($_FILES)) {
   foreach($_FILES AS $name => $value){
      $$name = $value['tmp_name'];
      foreach($value AS $namen => $valuen){ ${$name.'_'.$namen} = $value[$namen]; }
   }
}
?>
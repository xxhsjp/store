<?php
require_once __DIR__."/../../../include/login_filter_include.php";
require_once PATH_PROJ_INCLUDE."/tpl_mapping_include.php";

$tpl_name = isset($_POST['tpl_name']) ? $_POST['tpl_name'] : null;

if(empty($tpl_name) === true){
	echo "tpl name is null:", $tpl_name;
	exit;
}
//$tpl_mapping is defined in tpl_mapping_include.php
if(!isset($tpl_mapping[$tpl_name])){
	echo "tpl mapping is not found:", $tpl_name;
	exit;
}

$ext_tpl = ".html";
$tpl_file = PATH_PROJ_TPL.$tpl_mapping[$tpl_name].$ext_tpl;

if(!is_readable($tpl_file)){
	echo "tpl file is not found or not readable:", $tpl_name;
	exit;
}

$content = file_get_contents($tpl_file);

header("Content-Type:text/html; charset=utf-8");

echo $content;


?>


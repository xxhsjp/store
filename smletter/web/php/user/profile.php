<?php
require_once __DIR__."/../../../include/login_filter_include.php";


function profile() {
	header("Content-Type:application/json; charset=utf-8");
	
	$order = SessionManager::getEffectiveOrder();
	
	$info = SessionManager::getLoginInfo();
	
	$data = array("order"=>$order, "info"=>$info);
	
	echo json_encode($data);
}


?>


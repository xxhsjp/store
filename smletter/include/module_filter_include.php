<?php
require_once PATH_PROJ_ETC."conf_module.php";

$user_permission = SessionManager::getUserPermission();
if(empty($user_permission)){
	$lm = new LoginManager();
	$user_type = $lm->get_user_type();
	if(strcasecmp($user_type, "admin") === 0){
		$g_module_status['admin'] = true;
		$g_module_status['user'] = true;
	}else if(strcasecmp($user_type, "agent") === 0){
		$g_module_status['admin'] = true;
		$g_module_status['user'] = true;
	}else if(strcasecmp($user_type, "user") === 0){
		$g_module_status['admin'] = false;
		$g_module_status['user'] = true;
	}else {
		$g_module_status['admin'] = false;
		$g_module_status['user'] = false;
	}
	
	$audits = explode(",", TEMPLATE_AUDIT);
	foreach ($audits as $audit) {
		$account = SessionManager::getAccount();
		if(strcmp($account, trim($audit)) === 0){
			$g_module_status['audit'] = true;
		}
	}
	SessionManager::setUserPermission($g_module_status);
	$user_permission = $g_module_status;
}

$g_current_request_file = $_SERVER['PHP_SELF'];
$g_current_module_status = false;
if(isset($g_module_mapping[$g_current_request_file]) && $user_permission[$g_module_mapping[$g_current_request_file]] !== false){
	$g_current_module_status = $g_module_status[$g_module_mapping[$g_current_request_file]];
}else {
	echo 'Permission denied: ', $g_current_request_file;
	exit;
}


?>

<?php
require_once __DIR__."/base_include.php";

$g_loginManager = new LoginManager();

$g_isLogin = $g_loginManager->isLogin();
if($g_isLogin === false){
	header('Location: '.PROJ_ROOT.'login.php?msg=login');
	exit;
}

require_once __DIR__."/module_filter_include.php";

if(!empty($_POST['method']) || !empty($_GET['method'])){
	$method = empty($_POST['method'])?$_GET['method']:$_POST['method'];
	if(strpos($method, '_') === 0){
		Log::warn(basename(__FILE__).'@'.__LINE__.LOG_SEP.'call unexposed method: '.$method);
	}else {
		Log::debug(basename(__FILE__).'@'.__LINE__.LOG_SEP.'call method: '.$method);
		@call_user_func($method);
	}
	exit;
}

?>

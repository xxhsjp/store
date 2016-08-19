<?php
require_once __DIR__."/include/base_include.php";
require_once __DIR__."/include/user_view_include.php";

$error_msg = isset($_GET['er_msg']) ? $_GET['er_msg'] : null;
if(!empty($error_msg)){
	$msg = strtoupper($error_msg);
	$tips = @constant('LangLogin::'.$msg);
	$g_smarty->assign('error_msg', empty($tips) ? $error_msg : $tips);
}


$rc = new ReflectionClass('LangLogin');
$v = $rc->getConstants();

$langs = array();
foreach ($v as $name => $value){
	$k = strtolower($name);
	$langs[$k] = $value;
	$g_smarty->assign($k, $value);
}
$langs['g_cookie_prefix'] = PROJ_COOKIE_PREFIX;
$lang_json = json_encode($langs);

$account = CookieManager::getLoginAccount();
CookieManager::clearSessionId();
$g_smarty->assign('rem_login_account', $account);
$g_smarty->assign('lang_json', $lang_json);

$g_smarty->display('login.html');
//master 11
?>


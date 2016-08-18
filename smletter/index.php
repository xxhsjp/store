<?php
require_once __DIR__."/include/login_filter_include.php";
require_once __DIR__."/include/user_view_include.php";

$cls_names = array('LangMainPage', 'LangProfile', 'LangShare', 'LangUserSetting' , 'LangSent' , 'LangCompose', 'LangSubscribers');

$v= CommonUtils::getClassConstants($cls_names);

$langs = array();
foreach ($v as $name => $value){
	$k = strtolower($name);
	$langs[$k] = $value;
	$g_smarty->assign($k, $value);
}

$lang_json = json_encode($langs);

$account = SessionManager::getAccount();
$g_smarty->assign('account', $account);
$g_smarty->assign('lang_json', $lang_json);

$g_smarty->display('index.html');


?>


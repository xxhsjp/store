<?php
require_once __DIR__."/../../../include/base_include.php";

$account = isset($_POST['account']) ? $_POST['account'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$rem_me = isset($_POST['remember_me']) ? $_POST['remember_me'] : null;

if(empty($account) === true || empty($password) === true){
	header("Location: ".PROJ_ROOT."login.php?er_msg=".LoginManager::$responseMap['11911']);
}
$lm = new LoginManager();
$loginCode = $lm->login($account, $password);
if(strcmp($loginCode, "ok") === 0){
	CookieManager::setLoginAccount($account, strcmp($rem_me, "1") === 0);
	header("Location: ".PROJ_ROOT."index.php");
}else {
	header("Location: ".PROJ_ROOT."login.php?er_msg=".$loginCode);
}


?>


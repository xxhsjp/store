<?php
require_once __DIR__."/../../../include/base_include.php";

$account = isset($_POST['account']) ? $_POST['account'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;

if(empty($account) === true || empty($password) === true || empty($email) === true){
	header("Location: ".PROJ_ROOT."login.php?er_msg=".LoginManager::$responseMap['11911']);
}
$lm = new LoginManager();
$reg_code = $lm->register($account, $password);
$msg = 'register_success';
if(empty($reg_code)){
	$msg = 'register_failure';
}
header("Location: ".PROJ_ROOT."login.php?er_msg=".$msg);

?>


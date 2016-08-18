<?php
require_once __DIR__."/../../../include/base_include.php";
$user = isset($_POST['user']) ? $_POST['user'] : null;
$ems = isset($_POST['emails']) ? $_POST['emails'] : null;
if(empty($ems) === true || empty($user) === true) {
	echo '{"code":0}';
	exit;
}
$param = explode(',', $ems);

$account = _getAccount($user);
$sm = new SubscriberManager($account);

$res = $sm->add($param);
_export($res);

function _export($res) {
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($res);
	exit;
}
function _getAccount($_user) {
	return $_user.'@'.DEFAULT_DOMAIN;
}


	
?>
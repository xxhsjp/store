<?php
require_once __DIR__."/../../../include/login_filter_include.php";

function pwd() {
	$po = isset($_POST['pw_old']) ? $_POST['pw_old'] : null;
	$pn = isset($_POST['pw_new']) ? $_POST['pw_new'] : null;
	header("Content-Type:text/html; charset=utf-8");
	if(empty($po) === true || $pn===null) {
		echo '{"code":0}';
		exit;
	}
	$account = SessionManager::getAccount();
	$am = new AccountInfoManager($account);
	Log::oper_log($account.LOG_SEP.'update password:'.LOG_SEP."time".LOG_SEP.date(DATE_ATOM,time()));
	$r = $am->update_pwd($po, $pn);
	_export(array("result"=>$r));
}

function _export($res) {
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($res);
	exit;
}

// Log::oper_log($account.LOG_SEP.'Add Task '.LOG_SEP.$tpl_name.LOG_SEP.$addr_list.LOG_SEP.$mail_subject);
?>
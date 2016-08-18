<?php
require_once __DIR__."/../../../include/login_filter_include.php";


function _export($res) {
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($res);
	exit;
}

function query() {
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 400}';
		exit;
	}
	$limit_start = ($page-1)*$size;

	$account = SessionManager::getAccount();
	$am = new AccountInfoManager($account);
// 	$r = $am->query_by_page($group_id, array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
	_export('');
}


// Log::oper_log($account.LOG_SEP.'Add Task '.LOG_SEP.$tpl_name.LOG_SEP.$addr_list.LOG_SEP.$mail_subject);
?>
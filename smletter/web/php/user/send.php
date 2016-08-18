<?php
require_once __DIR__."/../../../include/login_filter_include.php";

function query() {
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 400}';
		exit;
	}
	$limit_start = ($page-1)*$size;
	
	$account = SessionManager::getAccount();
	$sm = new MailFromManager($account);
// 	$r = $sm->query();
	$r = $sm->query_by_page(array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
// 	Log::debug(__METHOD__.' @ '. $account . '   ' . json_encode($r));
	header("Content-Type:text/html; charset=utf-8");
	if(empty($r)){
		echo '{"code": 500}';
		exit;
	}
	echo json_encode($r);
}

function del() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	$account = SessionManager::getAccount();
	$sm = new MailFromManager($account);
	Log::oper_log($account.LOG_SEP.'del sendMailFrom:'.LOG_SEP.$id);
	$r = $sm->del($sm->sql_escape_string($id));
	header("Content-Type:text/html; charset=utf-8");
	echo $r;
}

function add() {
	$scc = isset($_POST['sd_acc']) ? $_POST['sd_acc'] : null;
	$srep = isset($_POST['sd_rep']) ? $_POST['sd_rep'] : null;
	$des = isset($_POST['sd_des']) ? $_POST['sd_des'] : null;
	$type = isset($_POST['sd_type']) ? $_POST['sd_type'] : null;
	header("Content-Type:text/html; charset=utf-8");
	if(empty($scc) === true || $type===null) {
		echo '{"code":0}';
		exit;
	}
	$account = SessionManager::getAccount();
	$sm = new MailFromManager($account);
	$addArray = array(
			'mail_from' => $sm->sql_escape_string($scc),
			'reply_to' => $sm->sql_escape_string($srep),
			'type' => $sm->sql_escape_string($type),
			'description' => $sm->sql_escape_string($des)
	);
	Log::oper_log($account.LOG_SEP.'add sendMailFrom:'.LOG_SEP.json_encode($addArray));	
	$res = $sm->add($addArray);
	if($res===null) {
		$res = 9;
	}
	echo '{"code":', $res,'}';
}

// Log::oper_log($account.LOG_SEP.'Add Task '.LOG_SEP.$tpl_name.LOG_SEP.$addr_list.LOG_SEP.$mail_subject);
?>
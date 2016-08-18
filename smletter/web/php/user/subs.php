<?php
require_once __DIR__."/../../../include/login_filter_include.php";


function _export($res) {
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($res);
	exit;
}
function _getAccount() {
	$account = SessionManager::getAccount();
	return $account.'@'.DEFAULT_DOMAIN;
}

function querySubs() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	$account = _getAccount();
	$sm = new SubscriberManager($account);
	$r = $sm->query_by_id($id);
	$r = empty($r)?[]:$r;
//	Log::debug(__METHOD__.LOG_SEP.$account.LOG_SEP.LOG_SEP.json_encode($r));
	_export($r);
}

function query() {
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 400}';
		exit;
	}
	$limit_start = ($page-1)*$size;

	$account = _getAccount();
	Log::debug(__METHOD__.LOG_SEP.$account.LOG_SEP.$size.LOG_SEP.$limit_start);
	$sm = new SubscriberManager($account);
	$r = $sm->query_by_page(array("order" => array("subscribed_time DESC"), "limit" => array($limit_start, $size)));
	_export($r);
}

function del() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	
	$account = _getAccount();
	$sm = new SubscriberManager($account);
	Log::oper_log($account.LOG_SEP.'del subscriber:'.LOG_SEP.$id);
	$res = $sm->del($id);
	_export($res);
}

function delall() {
	$account = _getAccount();
	$sm = new SubscriberManager($account);
	Log::oper_log($account.LOG_SEP.'del subscriber:'.LOG_SEP.$id);
	$res = $sm->del();
	_export($res);
}

function add() {
	$ems = isset($_POST['emails']) ? $_POST['emails'] : null;
	header("Content-Type:text/html; charset=utf-8");
	if(empty($ems) === true) {
		echo '{"code":0}';
		exit;
	}
	Log::debug($account.LOG_SEP.__METHOD__.LOG_SEP.json_encode($ems));
	$param = explode(',', $ems);
	Log::debug($account.LOG_SEP.__METHOD__.LOG_SEP.json_encode($param));
	
	$account = _getAccount();
	$sm = new SubscriberManager($account);

	$res = $sm->add($param);
	_export($res);
	
}

function update() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	$des = isset($_POST['des']) ? $_POST['des'] : null;
	header("Content-Type:text/html; charset=utf-8");
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	Log::oper_log($account.LOG_SEP.'update subscriber:'.LOG_SEP.$id);

	$account = _getAccount();
	$sm = new SubscriberManager($account);
	$param = array("id"=>$id, "description"=>$des);
	$res = $sm->update($param);
	_export($res);

}
// Log::oper_log($account.LOG_SEP.'Add Task '.LOG_SEP.$tpl_name.LOG_SEP.$addr_list.LOG_SEP.$mail_subject);
?>
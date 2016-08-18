<?php
require_once __DIR__."/../../../include/login_filter_include.php";

function _export($res) {
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($res);
	exit;
}

function _parameter($data) {
	$data = _array_remove($data, "method");
	$arr = array();
	foreach ($data as $key=>$val) {
		if($val!=="") {
			$arr[$key] = $val;
		}
	}
	return $arr;
}

function _array_remove($arr, $key){
	if(!array_key_exists($key, $arr)){
		return $arr;
	}
	$keys = array_keys($arr);
	$index = array_search($key, $keys);
	if($index !== FALSE){
		array_splice($arr, $index, 1);
	}
	return $arr;

}

function query() {
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	$aid = isset($_POST['aid']) ? $_POST['aid'] : '';
	if(empty($aid)||empty($page) || empty($size)){
		echo '{"code": 400}';
		exit;
	}
	$limit_start = ($page-1)*$size;

	$account = SessionManager::getAccount();
	$om = new OrderManager($account);
	$r = $om->query_by_page($aid, array("order" => array("id DESC"), "limit" => array($limit_start, $size)));
	// 	Log::debug(__METHOD__.' @ '. $account . '   ' . $agent_id);
	_export($r);
}

function del() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	$aid = isset($_POST['aid']) ? $_POST['aid'] : null;
	if(empty($id) === true||empty($aid) === true) {
		echo '{"code":0}';
		exit;
	}
	$account = SessionManager::getAccount();
	$om = new OrderManager($account);
	// 	$r = $am->del($am->sql_escape_string($id));
	$info = SessionManager::getLoginInfo();
	Log::oper_log($account.LOG_SEP.'del orders:'.LOG_SEP.$id);
	$r = $om->del($id, $aid);
	_export($r);
}

function add() {
	$aid = isset($_POST['account_id']) ? $_POST['account_id'] : null;
	if(empty($aid) === true) {
		_export(array("code"=>0));
		exit;
	}	
	$data = _parameter($_POST);
	$account = SessionManager::getAccount();
	$om = new OrderManager($account);
	Log::oper_log($account.LOG_SEP.'add orders:'.LOG_SEP.json_encode($_POST));
	$res = $om->add($data);
	if($res===null) {
		$res = 9;
	}
	_export(array("code"=>$res));
}

function update() {
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	$aid = isset($_POST['account_id']) ? $_POST['account_id'] : null;
	if(empty($id) === true||empty($aid) === true) {
		_export(array("code"=>0));
		exit;
	}	
	$data = $_POST;//_parameter($_POST);
	
	$account = SessionManager::getAccount();
	$om = new OrderManager($account);
	Log::oper_log($account.LOG_SEP.'update orders:'.LOG_SEP.json_encode($data));
	$res = $om->update($data);
	if($res===null) {
		$res = 9;
	}
	_export(array("code"=>$res));	
}

function type() {
	$filepath = PATH_PROJ_DATA.'ordertype.ini';
//	Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$filepath);
	_export(CommonUtils::get_ini_array($filepath));
}
?>
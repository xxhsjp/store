<?php
require_once __DIR__."/../../../include/login_filter_include.php";


function list_task() {
	header("Content-Type:application/json; charset=utf-8");
	
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 400}';
		exit;
	}
	$limit_start = ($page-1)*$size;
	$account = SessionManager::getAccount();
	$taskm = new TasksManager($account);
	$data = $taskm->query_by_page(array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
	if(empty($data)){
		echo '{"code": 500}';
		exit;
	}
	$time = time() + 300;
	$l = count($data['result']);
	for ($i = 0; $i < $l; $i++) {
		if($data['result'][$i]['timed_task'] === '1'){
			if((int)$data['result'][$i]['send_time'] < $time){
				$data['result'][$i]['has_send'] = true;
			}else {
				$data['result'][$i]['has_send'] = false;
			}
		}
// 		unset($data['result'][$i]['task_qid']);
	}
	echo json_encode($data);
}

function get_frm_info() {
	$account = SessionManager::getAccount();
	$addrm = new AddrsManager($account);
	$tplm = new TemplatesManager($account);
// 	$mfm = new MailFromManager($account);
	
	$addrs = $addrm->query();
	$tpls = $tplm->query(array("where"=>array("status"=>2)));
// 	$from = $mfm->query(array("where"=>array("type"=>1)));
	
	$data = array("addr" => $addrs, "tpl" => $tpls, "from" => null);
	
	header("Content-Type:application/json; charset=utf-8");
	echo json_encode($data);
}

function download_detail(){
	$mail_id = isset($_GET['mail_id']) ? $_GET['mail_id'] : '';
	if(empty($mail_id) === true) {
		echo "{\"code\": 400}";
		return;
	}
	$account = SessionManager::getAccount();
	$account_base = PROJ_WORK_DIR.$account;
	$base_file = $account_base.'/logs/'.$mail_id;
	$out_dir = $account_base.'/download/';
	$out_file = $out_dir.$mail_id.'.download.address.zip';
	$out_file_tmp = $out_file.'.tmp';
	
	if(!file_exists($out_dir)){
		mkdir($out_dir, 0777, true);
	}
	$df_ext = '.txt';
	$data = array(".ok" => LangTask::TASK_DOWNLOAD_DETAIL_OK.$df_ext, ".err" => LangTask::TASK_DOWNLOAD_DETAIL_ERR.$df_ext, ".err.blacklist_address" => LangTask::TASK_DOWNLOAD_DETAIL_BLK.$df_ext, ".err.invalid_address"=> LangTask::TASK_DOWNLOAD_DETAIL_INV.$df_ext);
	
	$zip = new ZipArchive();
	if($zip->open($out_file_tmp, ZipArchive::OVERWRITE) === true){
		foreach ($data as $key => $value) {
			if(file_exists($base_file.$key)){
				$zip->addFile($base_file.$key, CommonUtils::iconv("UTF-8", "GB18030", $value));
			}
		}
		
		$zip->close();
	}
	rename($out_file_tmp, $out_file);
	if(is_readable($out_file)){
		header('Content-Disposition: attachment; filename="address.zip"');
		header("Content-Type: application/zip; charset=utf-8");
		$zip_file = file_get_contents($out_file);
		unlink($out_file);
		echo $zip_file;
	}else {
		header("Content-Type: text/plain; charset=utf-8");
		echo LangTask::TASK_DETAIL_INFO1;
	}
}

function task_detail() {
	header("Content-Type:application/json; charset=utf-8");
	$account = SessionManager::getAccount();
	$mail_id = isset($_POST['mail_id']) ? $_POST['mail_id'] : '';
	if(empty($mail_id) === true) {
		echo "{\"code\": 400}";
		return;
	}
	$base_file = PROJ_WORK_DIR.$account.'/logs/'.$mail_id;
	$data = array(".ok" => 0, ".err.400" => 0, ".err.500" => 0, ".err.blacklist_address" => 0, ".err.invalid_address"=> 0);
	
	foreach ($data as $key => & $value) {
		$sum = CommonUtils::getLineCountOfFile($base_file.$key);
		$value = $sum;
	}
	
	echo json_encode($data);
}

function test_task() {
	header("Content-Type:application/json; charset=utf-8");
	$account = SessionManager::getAccount();
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$test_info = isset($_POST['task_test_data']) ? $_POST['task_test_data'] : '';
	
	if(empty($tid) === true || empty($test_info) === true) {
		echo "{\"code\": 400}";
		return;
	}
	
	$tm = new TasksManager($account);
	$rec = $tm->query(array("where" => array("id"=>$tid)));
	$r = _task_file_exist($rec[0]['tpl_file'], $rec[0]['addrlist_file']);
	if($r === false){
		echo "{\"code\": 404}";
		return;
	}
	$order = SessionManager::getEffectiveOrder();
	if(empty($order)){
		echo "{\"code\": 406}";
		return;
	}
	$iou_result = _increase_order_usedcount($order['id']);
	if(empty($iou_result)){
		echo "{\"code\": 424}";
		return;
	}
	SessionManager::increaseOrderUsedCount(1);
	$res = _add_task_q($rec[0], $test_info, null, 'test');
	$code = 200;
	if($res === -1){
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.'Fail to add task q');
		$code = 0;
	}
	
	Log::oper_log($account.LOG_SEP.'Test Task'.LOG_SEP.$rec[0]['tpl_name'].LOG_SEP.$rec[0]['addrlist_name'].LOG_SEP.$rec[0]['subject']);
	
	echo "{\"code\": $code}";
}

function del_task(){
	header("Content-Type:application/json; charset=utf-8");
	$account = SessionManager::getAccount();
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$is_time_task = isset($_POST['time_task']) ? $_POST['time_task'] : '';
	
	if(empty($tid) === true) {
		echo "{\"code\": 400}";
		return;
	}
	
	$tm = new TasksManager($account);
	$res = null;
	if($is_time_task === '1'){
		$es_tid = MySQLManager::sql_escape_string($tid);
		$time = time() + 300;
		$rec = $tm->query(array("where" => "id=$es_tid AND status=1 AND send_time>$time"));
		if(empty($rec)){
			echo "{\"code\": 400}";
			return;
		}
		$count = (int)$rec[0]['send_count'];
		$task_qid = $rec[0]['task_qid'];
		if(empty($task_qid)){
			echo "{\"code\": 404}";
			return;
		}
		
		$del_res = _del_task_q($task_qid, $rec[0]['level']);
		
		if(empty($del_res)){
			echo "{\"code\": 510}";
			return;
		}
		$order = SessionManager::getEffectiveOrder();
		if(empty($order)){
			echo "{\"code\": 406}";
			return;
		}
		$iou_result = _increase_order_usedcount($order['id'], -$count);
		if(empty($iou_result)){
			echo "{\"code\": 424}";
			return;
		}
		SessionManager::increaseOrderUsedCount(-$count);
		$res = $tm->del($tid, 1);
	}else {
		$res = $tm->del($tid, 0);
	}
	
	$code = 200;
	if(empty($res)){
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.'File to del task: '.$tid);
		$code = 500;
	}
	Log::oper_log($account.LOG_SEP.'Remove Task'.LOG_SEP.$tid);
	
	echo "{\"code\": $code}";
}

function start_task() {
	header("Content-Type:application/json; charset=utf-8");
	$account = SessionManager::getAccount();
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$send_time = time();
	$is_time_task = false;
	if(isset($_POST['send_time'])){
		$send_time = strtotime($_POST['send_time']);
		if(empty($send_time)){
			echo "{\"code\": 400}";
			return;
		}
		$is_time_task = true;
	}
	
	if(empty($tid) === true) {
		echo "{\"code\": 400}";
		return;
	}

	$tm = new TasksManager($account);
	$rec = $tm->query(array("where" => array("id"=>$tid, "status"=>0)));
	if(empty($rec)){
		echo "{\"code\": 400}";
		return;
	}
	$r = _task_file_exist($rec[0]['tpl_file'], $rec[0]['addrlist_file']);
	if($r === false){
		echo "{\"code\": 404}";
		return;
	}
	$order = SessionManager::getEffectiveOrder();
	if(empty($order)){
		echo "{\"code\": 406}";
		return;
	}
	$rec[0]['send_time'] = $send_time;
	
	$count_today = $tm->send_count_today($send_time);
	$count_today = empty($count_today) ? 0 : $count_today;
	$send_today = (int)$rec[0]['send_count'] + $count_today;
	$daily_send_limit = empty($order['daily_send_limit']) ? 0 : $order['daily_send_limit'];
	if($send_today > $daily_send_limit){
		echo "{\"code\": 407}";
		return;
	}
	
	$iou_result = _increase_order_usedcount($order['id'], $rec[0]['send_count']);
	if(empty($iou_result)){
		echo "{\"code\": 424}";
		return;
	}
	$info = SessionManager::getLoginInfo();
	$lv = (int)$info['level'];
	$qid = _add_task_q($rec[0], null, $is_time_task, $lv);
	if($qid === -1){
		$iou_result2 = _increase_order_usedcount($order['id'], -(int)$rec[0]['send_count'], true);
		echo "{\"code\": 409}";
		return;
	}
	
	$set = array("status"=>1, "send_time"=>$send_time, "id"=>$tid, "task_qid"=>$qid, "level"=>$lv);
	if($is_time_task){
		$set['timed_task'] = 1;
	}
	$update_result = $tm->update($set);
	$code = 200;
	
	if(empty($update_result)){
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.'File to add task q');
		$code = 0;
	}else {
		SessionManager::increaseOrderUsedCount($rec[0]['send_count']);
	}
	Log::oper_log($account.LOG_SEP.($is_time_task?'Time Task':'Start Task').LOG_SEP.$rec[0]['tpl_name'].LOG_SEP.$rec[0]['addrlist_name'].LOG_SEP.$rec[0]['subject']);
	
	echo "{\"code\": $code}";
}

function add_task(){
	header("Content-Type:application/json; charset=utf-8");
	
	$tpl_id = isset($_POST['tpl_id']) ? $_POST['tpl_id'] : '';
	$addr_id = isset($_POST['addr_id']) ? $_POST['addr_id'] : '';
// 	$mail_subject = isset($_POST['mail_subject']) ? $_POST['mail_subject'] : '';
	$from_addr = isset($_POST['from_addr']) ? $_POST['from_addr'] : '';
	$reply_to = isset($_POST['reply_to']) ? $_POST['reply_to'] : '';
	
	if(empty($from_addr) || empty($tpl_id) || empty($addr_id)){
		echo "{\"code\": 400}";
		return;
	}
	
	$account = SessionManager::getAccount();
	$tplm = new TemplatesManager($account);
	$tpl_info = $tplm->query(array("where"=>array("id"=>$tpl_id,"status"=>2)));
	if(empty($tpl_info)){
		echo "{\"code\": 403}";
		return;
	}
	$tpl_info = $tpl_info[0];
	
	$addrm = new AddrsManager($account);
	$addr_info = $addrm->query(array("where"=>array("id"=>$addr_id)));
	if(empty($addr_info)){
		echo "{\"code\": 403}";
		return;
	}
	$addr_info = $addr_info[0];
	
	$mail_from = 'RANDOM';
	$mail_from_type = '1';
	$addr_list = $addr_info['addrlist_name'];
	$addr_list_file = $addr_info['addrlist_file'];
	$addrs_sum = empty($addr_info['addrs_sum']) ? 1 : $addr_info['addrs_sum'];
	$tpl_name = $tpl_info['tpl_name'];
	$tpl_file = $tpl_info['tpl_file'];
	$mail_subject = $tpl_info['subject'];
	
	if(empty($addr_list) || !is_numeric($addr_list_file)
			|| empty($tpl_name) || empty($tpl_file)
	){
		echo "{\"code\": 424}";
		return;
	}
	$addr_list_file = 'send_addrlist_'.$addr_list_file.'.db';
	if(mb_strlen($mail_subject, 'utf-8') > 200 || mb_strlen($from_addr, 'utf-8') > 100){
		$mail_subject = mb_substr($mail_subject, 0, 200, 'utf-8');
		$from_addr = mb_substr($from_addr, 0, 100, 'utf-8');
	}
	
	$mail_id = CommonUtils::generate_mail_id();
	$raw_from = CommonUtils::encode_rfc2047_mail($from_addr);
	
	$rec = array(
			'tpl_name' => $tpl_name,
			'tpl_file' => $tpl_file,
			'tpl_id' => $tpl_id,
			'addrlist_name' => $addr_list,
			'addrlist_file' => $addr_list_file,
			'addrlist_id' => $addr_id,
			'mail_from' => $mail_from,
			'from' => $from_addr,
			'raw_from' => $raw_from,
			'reply_to' => $reply_to,
			'raw_reply_to' => CommonUtils::encode_rfc2047_mail($reply_to),
			'subject' => $mail_subject,
			'raw_subject' => CommonUtils::encode_rfc2047($mail_subject),
			'mail_id' => $mail_id,
			'mail_from_type' => $mail_from_type,
			'status' => '0',
			'tag' => '',
			'type' => '1',
			'timed_task' => '0',
			'task_qid' => '',
			'level' => '',
			'send_count' => $addrs_sum);

	$count = _add_task($rec);
	$code = empty($count) ? 500 : 200;
	
	Log::oper_log($account.LOG_SEP.'Add Task'.LOG_SEP.$tpl_name.LOG_SEP.$addr_list.LOG_SEP.$mail_subject);
	
	echo "{\"code\": $code}";
}

function _del_task_q($qid, $lv){
	$q_name = '';
	if($lv === 'test'){
		$q_name = TasksQManager::Q_TEST;
	}else {
		$lv = (int)$lv;
		if($lv > 5){
			$lv = 5;
		}
		if($lv < 0){
			$lv = 0;
		}
		$q_name = @constant('TasksQManager::Q_LV'.$lv);
	}
	if(empty($q_name)){
		$q_name = TasksQManager::Q_LV1;
	}
	$tmq = new TasksQManager($q_name);
	$r = $tmq->del($qid);
	
	return $r;
}

function _add_task_q(array $rec, $test_info=null, $is_time_task=null, $lv){
	$account = SessionManager::getAccount();
	$q_name = '';
	if($lv === 'test'){
		$q_name = TasksQManager::Q_TEST;
	}else {
		$lv = (int)$lv;
		if($lv > 5){
			$lv = 5;
		}
		if($lv < 0){
			$lv = 0;
		}
		$q_name = @constant('TasksQManager::Q_LV'.$lv);
	}
	if(empty($q_name)){
		$q_name = TasksQManager::Q_LV1;
	}
	
	$tmq = new TasksQManager($q_name);
	$qid = $tmq->get_curr_id();
	$rec_q = array(
		"id" => $qid,
		"account" => $account,
		"level" => $lv,
		"testinfo" => $test_info,
		"sendtime" => $rec['send_time'],
		"mailfrom" => 'RANDOM',
		"from" => $rec['raw_from'],
		"replyto" => $rec['raw_reply_to'],
		"subject" => $rec['raw_subject'],
		"template" => $rec['tpl_file'],
		"addrlist" => $rec['addrlist_file'],
		"mailid" => $rec['mail_id'],
		"type" => $is_time_task === true ? '1' : '0'
	);
	$r = $tmq->add($rec_q);
	if(empty($r)){
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.'Fail to add task qid: '.$qid.' queue:'.$q_name);
		return -1;
	}
	
	return $qid;
}

function _add_task(array $rec){
	$account = SessionManager::getAccount();
	$taskm = new TasksManager($account);
	$count = $taskm->add($rec);
	
	return $count;
}

function _increase_order_usedcount($order_id, $increase_by=1, $is_sys=false){
	$aim = new AccountInfoManager();
	$account = SessionManager::getAccount();
	$result = $aim->increase_used_count($order_id, $increase_by);
	$oper = 'Proceeds';
	if((int)$increase_by < 0){
		$oper = 'Refund';
	}
	if(empty($result)){
		$oper .= ' Failed';
	}
	if($is_sys === true){
		$oper = '+'.$oper;
	}
	Log::w_log($account.LOG_SEP.$oper.LOG_SEP.$order_id.LOG_SEP.$increase_by, LOG_OUTPUT_DATA_FILE);
	
	return $result;
}

function _update_count($tpl_id){
	$account = SessionManager::getAccount();
	$tplm = new TemplatesManager($account);
	//to do
	$count = $tplm->increaseUsedCount($tpl_id);
	
	return $count;
}

function _task_file_exist($tpl_file, $addr_file){
	//to do
	return true;
}

?>


<?php
require_once __DIR__.'/../common/simple_fastdfs_client.php';
require_once __DIR__."/../../../include/login_filter_include.php";

//列表
function getList() {
	$account = SessionManager::getAccount();
	$atm = new AuditTemplatesManager($account);
	
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 0}';
		exit;
	}
	$limit_start = ($page-1)*$size;
	$dataList = $atm->query_by_page(array("order" => array("last_modify_time DESC"), "limit" => array($limit_start, $size)));
	if(empty($dataList)){
		echo '{"code": 900}';
		exit;
	}
	
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($dataList);
}

function getOneAudit($id){
	$admin = SessionManager::getAccount();
	$atm = new AuditTemplatesManager($admin);
	$resAuditOne = array();
	$resAuditOne = $atm->queryOne($id);
	return $resAuditOne;
}

function getAuditTplDescription($n){
	$n = (int)$n;
	$arrayDesc = array(LangTpls::TEMPLATE_AUDIT_FAIL_REASON_1,
						LangTpls::TEMPLATE_AUDIT_FAIL_REASON_2,
						LangTpls::TEMPLATE_AUDIT_FAIL_REASON_3,
						LangTpls::TEMPLATE_AUDIT_FAIL_REASON_4,
						LangTpls::TEMPLATE_AUDIT_FAIL_REASON_5
					);
	
	return $arrayDesc[$n];
}

//审核
function auditManageTpl(){
	header("Content-Type:application/json; charset=utf-8");
	$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
	$flag = isset($_POST['flag']) ? $_POST['flag'] : $_GET['flag'];
	$tplD = isset($_POST['tplD']) ? $_POST['tplD'] : $_GET['tplD'];
	$tplTip = isset($_POST['tplTip']) ? $_POST['tplTip'] : 0;
	$writeTip = isset($_POST['wTTip']) ? $_POST['wTTip'] : '';
	$writeTip = str_replace(LangTpls::TEMPLATE_AUDIT_SELECT_REASON,"",$writeTip);
	$tplA = isset($_POST['tplA']) ? $_POST['tplA'] : $_GET['tplA'];
	$tplA = urldecode($tplA);

	if(empty($id) === true || empty($tplD) === true ||empty($flag) === true || ($flag != '1' && $flag != '2')) {
		echo '{"code":901}';
		exit;
	}
	
	$description = getAuditTplDescription($tplTip);

	if($flag == "1"){//通过
		$status_check = 1;
		$status_tpl = 2;
		$tplTip = 0;
		$writeTip = '';
	}else{
		$status_check = 2;
		$status_tpl = 3;
	}
	
	//session存的管理员账号
	$admin = SessionManager::getAccount();
	
	//是否在此管理员组内
	/* $loginInfo 	= SessionManager::getLoginInfo();
	$agentId 	= $loginInfo['agent_id'];
	
	$accountIM 	= new AccountInfoManager();
	$resUser	= $accountIM->is_in_group($tplA,$agentId);
	if($resUser == 0){
		echo '{"code":904}';
		exit;
	} */
	
	//更新审核表
	$atm = new AuditTemplatesManager($admin);
	$resAudit = $atm->update(array('id' => $id, 'status' => $status_check, 'description' => $tplTip, 'extend1' => $writeTip));
	
	//数据无更新不记错，除非出现其他错误
	/* if($resAudit === null){
		echo '{"code":903}';
		exit;
	} */
	
	//更新模板表
	$tm = new TemplatesManager($tplA);
	$res = $tm->update(array('id' => $tplD, 'status' => $status_tpl, 'description' => $tplTip, 'extend_1' => $writeTip));
	
	if($resAudit != 200){
		echo '{"code":'.$resAudit.'}';
	}
	
	Log::oper_log($admin.LOG_SEP.'admin audit Tpl '.LOG_SEP.$tplA.LOG_SEP.$tplD.LOG_SEP.$flag.LOG_SEP.$resAudit.LOG_SEP.$tplTip.LOG_SEP.$writeTip);
	exit;
}

//审核预览
function showTpl(){
	header("Content-Type:text/html; charset=utf-8");
	$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
	
	if(empty($id) === true){
		echo '{"code":901}';
		exit;
	}
	
	$admin = SessionManager::getAccount();
	$atm = new AuditTemplatesManager($admin);
	$resOne = $atm->queryOne($id);
	
	if(count($resOne[0]) == 0) {
		echo '{"code":0}';
		exit;
	}
	
	$tplIndex = $resOne[0]["tpl_file"];
	$indexUrl = createDownloadLink("group1#".$tplIndex);
	$content = file_get_contents($indexUrl);
	
	echo $content;
	exit;
}

?>
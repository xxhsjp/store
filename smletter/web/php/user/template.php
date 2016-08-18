<?php
require_once __DIR__.'/../common/simple_fastdfs_client.php';
require_once __DIR__."/../../../include/login_filter_include.php";
//列表
function getList() {
	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	if(empty($page) || empty($size)){
		echo '{"code": 0}';
		exit;
	}
	$limit_start = ($page-1)*$size;
	$dataList = $tm->query_by_page(array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
	if(empty($dataList)){
		echo '{"code": 900}';
		exit;
	}
	
	$order = SessionManager::getEffectiveOrder();
	$data = array("data"=>$dataList, "orderNum"=>$order['tpl_count_limit']);
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($data);
}

//提交审核
function auditPostTpl(){
	header("Content-Type:text/html; charset=utf-8");
	
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	
	$account = SessionManager::getAccount();
	
	//查询状态 是否 未审核
	$tm = new TemplatesManager($account);
	$resOne = $tm->queryOne($id);

	if(count($resOne[0]) == 0) {//查不到记录或有审核状态的情况，报错
		echo '{"code":914}';
		exit;
	}
	
	if($resOne[0]["status"] == "1" || $resOne[0]["status"] == "2" ){
		echo '{"code":922}';
		exit;
	}
	
	$res = $tm->update(array('id' => $id, 'status' => 1, 'description' => 0, 'extend_1' => ''));
		
	//给审核者提交数据
	$postAudit = array ();
	$postAudit["tpl_id"] = $id;
	$postAudit["tpl_name"] = $resOne[0]["tpl_name"];
	$postAudit["tpl_file"] = $resOne[0]["tpl_file"];
	$postAudit["subject"] = $resOne[0]["subject"];
	$postAudit["account"] = $account;
	
	//模板审核人，目前配置1个，多个审核人配置时用逗号分隔，对这些审核人随机分配审核的记录。
	/* $auditArray = explode(",", TEMPLATE_AUDIT);
	 $tmp = mt_rand(0,4);
	 $adminAccount = $auditArray[$tmp]; */

	$adminAccount = TEMPLATE_AUDIT;
	$atm = new AuditTemplatesManager($adminAccount);
	$resOneAudit = $atm->queryOne($id);
	if(count($resOneAudit[0]) == 0){
		$resAudit = $atm->add($postAudit);
	}else{
		$postAudit["id"] = $resOneAudit[0]["id"];
		$postAudit["status"] = 0;
		$postAudit["description"] = 0;
		$postAudit["extend1"] = "";
		$resAudit = $atm->update($postAudit);
	}
	
	if($res != 200){
		echo '{"code":'.$res.'}';
	}
	Log::oper_log($account.LOG_SEP.'audit post Tpl '.LOG_SEP.$postAudit["tpl_name"].LOG_SEP.$postAudit["tpl_file"].LOG_SEP.$adminAccount.LOG_SEP.$res.LOG_SEP.$resAudit);
	exit;
}

//删除模板文件夹及文件
function delTplAll($dir,$flag = 0) {
	//先删除目录下的文件：
	$dh=opendir($dir);
	while($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				delTplAll($fullpath);
			}
		}
	}

	closedir($dh);
	//删除当前文件夹
	if($flag > 0){
		return true;
	}
	if(rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}

//删除模板
function delTpl() {
	header("Content-Type:text/html; charset=utf-8");
	$id = isset($_POST['id']) ? $_POST['id'] : null;
	if(empty($id) === true) {
		echo '{"code":0}';
		exit;
	}
	
	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$resOne = $tm->queryOne($id);
	
	if(count($resOne[0]) == 0) {
		echo '{"code":0}';
		exit;
	}
	
	$tplIndex = $resOne[0]["tpl_file"];
	removeFile($tplIndex);

	$existFile = isFileExists($tplIndex);
	if(isFileExists($existFile) === 1){
		echo '{"code":907}';
		exit;
	}
	
	$tm = new TemplatesManager($account);
	$res = $tm->del($id);
	
	if($res === null){
		echo '{"code": 907}';
		exit;
	}
	
	//找添加到哪个审核表的审核人，来删除记录

	$adminAccount = TEMPLATE_AUDIT;
	$atm = new AuditTemplatesManager($adminAccount);
	$resAudit = $atm->del(array("tpl_id"=>$id, "account"=>$account));
	
	if($res != 200){
		echo '{"code":'.$res.'}';
	}
	
	Log::oper_log($account.LOG_SEP.'Del Tpl '.LOG_SEP.$tplIndex.LOG_SEP.$res.LOG_SEP.$resAudit.LOG_SEP.$id);
	exit;
}

//预览
function previewTpl(){
	header("Content-Type:text/html; charset=utf-8");
	$tplId = isset($_POST['tplId']) ? $_POST['tplId'] : $_GET['tplId'];

	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$resOne = $tm->queryOne($tplId);
	
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

//编辑触发
function showTpl(){
	header("Content-Type:text/html; charset=utf-8");
	$id = isset($_POST['tplId']) ? $_POST['tplId'] : $_GET['tplId'];
	
	if(empty($id)){
		echo '{"code":0}';
		exit;
	}
	
	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$resOne = $tm->queryOne($id);
	
	if(count($resOne[0]) == 0) {
		echo '{"code":0}';
		exit;
	}
	
	$tplIndex = $resOne[0]["tpl_file"];
	$indexUrl = createDownloadLink("group1#".$tplIndex);
	$content = file_get_contents($indexUrl);
	
	$data = array("tplHtml"=>$content);
	echo json_encode($data);
	exit;
}

//编辑模板，只能改文字，不能改图片等
function editTpl(){
	header("Content-Type:application/json; charset=utf-8");
	$tplSubject = isset($_POST['tplSubjectDisp']) ? $_POST['tplSubjectDisp'] : null;
	$tplContent = isset($_POST['tplContent']) ? $_POST['tplContent'] : null;
	$tplId = isset($_POST['tplId']) ? $_POST['tplId'] : null;
	
	if(empty($tplSubject) || empty($tplContent) || empty($tplId)){
		echo '{"code":0}';
		exit;
	}
	
	if(strlen(trim($tplSubject)) < 1){
		echo '{"code":918}';
		exit;
	}
	
	$subjectLen = CommonUtils::str_byte_length($tplSubject);
	if($subjectLen > 200){
		echo '{"code":917}';
		exit;
	}
	
	$contentLen = CommonUtils::str_byte_length($tplContent);
	if($contentLen > UPLOAD_FILE_SIZE * 1024){
		echo '{"code":913}';
		exit;
	}
	
	//查tpl_file
	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$resOne = $tm->queryOne($tplId);
	
	if(count($resOne[0]) == 0) {
		echo '{"code":0}';
		exit;
	}
	
	$status = $resOne[0]["status"];
	if($status == "1" || $status == "2"){
		echo '{"code":922}';
		exit;
	}

	$fname = CommonUtils::generate_random_name();
	$folder = PROJ_WORK_DIR.$account."/template/".$fname;
	createFolder($folder);
	file_put_contents($folder."/index.html", $tplContent);
	
	$newDfs = updateFile("group1#".$resOne[0]["tpl_file"],$folder."/index.html");
	
	if(isFileExists($newDfs) !== 1){
		echo '{"code":921}';
		exit;
	}
	
	$newDfsTmp = explode("#", $newDfs)[1];
	$tm = new TemplatesManager($account);
	$res = $tm->update(array(
			'tpl_file' => $newDfsTmp,
			'subject' => $tplSubject,
			'status' => 0,
			'description' => 0,
			'extend_1' => '',
			'id' => $tplId 
	));
	
	if ($res === null) {
		$res = 902;
	}
	
	if(file_exists($folder)) {
		try{
			delTplAll($folder);
		}catch(Exception $e) {
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.' '. $folder.$fname . '  '. $e->getMessage());
			echo '{"code": 0}';
			exit;
		}
	}
	
	if($res != 200){
		echo '{"code":'.$res.'}';
	}
	
	Log::oper_log($account.LOG_SEP.'edit Tpl '.LOG_SEP.$newDfs.LOG_SEP.$res.LOG_SEP.$tplId);
	exit;
}

//添加模板：上传模板压缩包
function addTpl() {
	header("Content-Type:application/json; charset=utf-8");
	$tplName = isset($_POST['inputTplName']) ? $_POST['inputTplName'] : null;
	$tplSubject = isset($_POST['inputTplSubject']) ? $_POST['inputTplSubject'] : null;
	
	//zip包
	if(empty($tplName) || empty($tplSubject)) {
		echo '{"code":0}';
		exit;
	}

	if(strlen(trim($tplName)) < 1){
		echo '{"code":919}';
		exit;
	}
	
	if(strlen(trim($tplSubject)) < 1){
		echo '{"code":918}';
		exit;
	}
	
	$subjectLen = CommonUtils::str_byte_length($tplSubject);
	if($subjectLen > 200){
		echo '{"code":917}';
		exit;
	}
	
	//是否开通，存在订单
	$orderInfo = SessionManager::getEffectiveOrder();
	if(empty($orderInfo) == true){
		echo '{"code":911}';
		exit;
	}
	
	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$dataNum = $tm->queryCount();

	//是否超过上限
	if(($dataNum >= $orderInfo['tpl_count_limit'])){
		echo '{"code":912}';
		exit;
	}
	
	$file = $_FILES ['inputTplfile'];
	if ($file ['error'] > 0) {
		switch ($file ['error']) {
			case UPLOAD_ERR_OK :
				break;
			case UPLOAD_ERR_NO_FILE :
				// throw new RuntimeException('No file sent.');
				$res = 4;
			case UPLOAD_ERR_INI_SIZE :
			case UPLOAD_ERR_FORM_SIZE :
				// throw new RuntimeException('Exceeded filesize limit.');
				$res = 5;
			default :
				// throw new RuntimeException('Unknown errors.');
				$res = 6;
		}
		if (empty ($res) === false) {
			echo '{"code":'.$res.'}';
			exit;
		}
	} else {
		if(($file['name'] !== 'send.zip') || !checkZipType($file ['type'])){
			echo '{"code":903}';
			exit;
		}
		
		//压缩包大小限制1M
		if ($file ['size'] > UPLOAD_ZIP_SIZE * 1024) {
			echo '{"code":915}';
			exit;
		}
		
		$fname = CommonUtils::generate_random_name();
		$folder = PROJ_WORK_DIR.$account."/template/".$fname;
		createFolder($folder);
		
		//解压
		$zip = new ZipArchive();
		$zipRes = $zip->open($file["tmp_name"]);
		
		if($zipRes === true){
			$zip->extractTo($folder);
			$zip->close();
		}else{
			echo '{"code":'.$zipRes.'}'; //解压失败
			exit;
		}

		if(file_exists($folder."/index.htm")){
			$fileZName = $folder."/index.htm";
		}else if(file_exists($folder."/index.html")){
			$fileZName = $folder."/index.html";
		}else{
			echo '{"code":903}'; //文件不存在
			exit;
		}
		
		//文件不超过100K
		$fileSize = abs(filesize($fileZName));
		if($fileSize > UPLOAD_FILE_SIZE * 1024){
			echo '{"code":913}';
			exit;
		}

		//字符集转码
		$UFContent1 = file_get_contents($fileZName);
		$UFContent = CommonUtils::str_conv_to_utf8 ($UFContent1);
		
		//替换图片
		$imgPath = $folder."/images";
		preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/", $UFContent,$matches);
		if(count($matches[3]) > 0){
			if(is_dir($imgPath)){
				foreach($matches[3] as $key=>$val){
					$picUrl = uploadTplFile($folder."/".$val); //处理图片并得到处理后的地址
					$UFContent = str_replace($val,$picUrl,$UFContent);
				}
			}else{
				echo '{"code":903}';
				exit;
			}
		}
		
		//$tplZipContent = preg_replace('/(<img.+src=\"?.+)(images)\/(.+\.(jpg|gif|jpeg|png)\"?).+>/i',"\${1} http://localhost/edm/web/php/user/loadPic.php?u=".$account."&f=".$fname."&p=\${3}>",$UFContent);
		file_put_contents ($fileZName, $UFContent);
		
		//存index.html
		$indexPath = uploadFile($fileZName);
		if(isFileExists($indexPath) !== 1){
			echo '{"code":920}';
			exit;
		}
		
		//group1#M00/00/08/rBeyXlb-ELCARtmnAAAiZEoMH2A616.html
		$indexUrlArr = explode("#", $indexPath)[1];
		//$indexUrlArrTmp = explode("/", $indexUrlArr);
		//$indexUrl = implode("|", $indexUrlArrTmp);
		$indexUrl = $indexUrlArr;
		
		$tm = new TemplatesManager($account);
		$res = $tm->add(array(
				'tpl_name' => $tplName,
				'tpl_file' => $indexUrl,
				'subject' => $tplSubject,
				'status' => 0 
		));
		
		if ($res === null) {
			$res = 902;
		}else{
			delTplAll($folder);
		}
	}

	if($res != 200){
		echo '{"code":'.$res.'}';
	}

	Log::oper_log($account.LOG_SEP.'Add Tpl '.LOG_SEP.$tplName.LOG_SEP.$fname.LOG_SEP.$folder.LOG_SEP.$res);
	exit;
}

//添加模板：上传文件、编辑框编写模板
function addTplAll() {
	header("Content-Type:application/json; charset=utf-8");
	$tplName = isset($_POST['inputTplName']) ? $_POST['inputTplName'] : null;
	$tplSubject = isset($_POST['inputTplSubject']) ? $_POST['inputTplSubject'] : null;
	$tplFlag = isset($_POST['tplMethod']) ? $_POST['tplMethod'] : '1';
	$tplContent = isset($_POST['tplContent']) ? $_POST['tplContent'] : null;
	$okFlag = 0;
	
	//zip包
	if(empty($tplName) || empty($tplSubject)) {
		echo '{"code":0}';
		exit;
	}
	
	if(strlen(trim($tplName)) < 1){
		echo '{"code":919}';
		exit;
	}
	
	if(strlen(trim($tplSubject)) < 1){
		echo '{"code":918}';
		exit;
	}
	
	//是否开通，存在订单
	$orderInfo = SessionManager::getEffectiveOrder();
	if(empty($orderInfo) == true){
		echo '{"code":911}';
		exit;
	}

	$account = SessionManager::getAccount();
	$tm = new TemplatesManager($account);
	$dataNum = $tm->queryCount();
	
	//是否超过上限
	if(($dataNum >= $orderInfo['tpl_count_limit'])){
		echo '{"code":912}';
		exit;
	}
	
	if(empty($tplName) || ($tplFlag == '0' && empty($tplContent))) {
		echo '{"code":0}';
		exit;
	}
	
	$subjectLen = CommonUtils::str_byte_length($tplSubject);
	if($subjectLen > 200){
		echo '{"code":917}';
		exit;
	}
	
	if($tplFlag == '1'){
		$file = $_FILES['inputTplfile'];
		if($file['error'] > 0) {
			switch ($file['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					//throw new RuntimeException('No file sent.');
					$res = 4;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					//throw new RuntimeException('Exceeded filesize limit.');
					$res = 5;
				default:
					//throw new RuntimeException('Unknown errors.');
					$res = 6;
			}
			if(empty($res) === false){
				echo '{"code":'.$res.'}';
				exit;
			}
		} else {
			$t = $file['type'];
			if(!checkType($t)){
				echo '{"code":903}';
				exit;
			}
			
			if($file['size'] > UPLOAD_FILE_SIZE * 1024){
				echo '{"code":913}';
				exit;
			}
			
			$okFlag = 1;
		}
	}else{
		$contentLen = CommonUtils::str_byte_length($tplContent);
		if($contentLen > UPLOAD_FILE_SIZE * 1024){
			echo '{"code":913}';
			exit;
		}
		$okFlag = 1;
	}
	
	if($okFlag == 1){
		$fname = CommonUtils::generate_random_name();
		$folder = PROJ_WORK_DIR.$account."/template/";
		createFolder($folder);
		$tm = new TemplatesManager($account);
		$res = $tm->add(array(
				'tpl_name' => $tplName,
				'tpl_file' => $fname,
				'subject' => $tplSubject,
				'status' => 0
		));
		
		if($res===null) {
			$res = 902;
		}else{
			if($tplFlag == '1'){
				$UFContent1 = file_get_contents($file["tmp_name"]);
				$UFContent = CommonUtils::str_conv_to_utf8($UFContent1);
				file_put_contents($file["tmp_name"], $UFContent);
				//Log::debug("before=======              ".$UFContent1."         after======== ".$UFContent);
				move_uploaded_file($file["tmp_name"], $folder.$fname);
			}else{
				//Log::debug("before=======              ".$tplContent);
				//$tplContent = CommonUtils::str_conv_to_utf8($tplContent);
				//Log::debug("after=======              ".$tplContent);
				file_put_contents($folder.$fname, $tplContent);
			}
		}
	}
	
	$okFlag = 0;
	
	if($res != 200){
		echo '{"code":'.$res.'}';
	}
	
	Log::oper_log($account.LOG_SEP.'Add Tpl All'.LOG_SEP.$tplName.LOG_SEP.$fname.LOG_SEP.$res);
	exit;
}

//校验上传文件类型  txt html
function checkType($type) {
	return (!(strpos($type, 'text')===false)||!(strpos($type, 'html')===false));
}

//校验zip包
function checkZipType($type){
	return (!(strpos($type, 'zip')===false)||!(strpos($type, 'x-zip-compressed')===false));
}

function createFolder($fileFolder) {
	if(!file_exists($fileFolder)) {
		mkdir($fileFolder, 0777, true);
	}
}

//dfs存图片，取图片url
function uploadTplFile($p)
{
	$location = uploadFile($p);
	$pic_name = createDownloadLink($location);
	return $pic_name;
}

?>
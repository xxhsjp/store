<?php
require_once __DIR__."/../../../include/login_filter_include.php";


function _checkLimit($count) {
	$Order= SessionManager::getEffectiveOrder();
	$limit = $Order["addrlist_limit"];
	return ($limit - $count >=0);
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
	$am = new AddrsManager($account);
// 	$r = $am->query();
	$r = $am->query_by_page(array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
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
	$am = new AddrsManager($account);
	Log::oper_log($account.LOG_SEP.'del addressList:'.LOG_SEP.$id);
	$r = $am->del($am->sql_escape_string($id));
	if("1"===$r&&isset($_POST['file'])) {
		$file = $_POST['file'];
		$alm = new AddrListManager($account, $file);
		$res = $alm->del();
		Log::oper_log($account.__METHOD__.LOG_SEP.'del db:'.LOG_SEP.json_encode($res));
	}
	header("Content-Type:text/html; charset=utf-8");
	echo $r;
}

function add() {
	$lname = isset($_POST['ar_lname']) ? $_POST['ar_lname'] : null;
	$als = $lname;//isset($_POST['ar_als']) ? $_POST['ar_als'] : null;
	$des = isset($_POST['ar_des']) ? $_POST['ar_des'] : null;
	header("Content-Type:text/html; charset=utf-8");
	if(empty($lname) === true) {
		echo '{"code":0}';
		exit;
	}
	$file = $_FILES['ar_file'];
	if($file['error'] > 0) {
		switch ($file['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				$r = 91;	//throw new RuntimeException('No file sent.');
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$r = 92;	//throw new RuntimeException('Exceeded filesize limit.');
				break;
			default:
				$r = 90;	//throw new RuntimeException('Unknown errors.');
		}
		echo '{"code":'.$r.'}';
		exit;
	} else {
 		$n = $file['name'];
		$t = $file['type'];
		if(_checkType($t)) {
			$tmpn = pathinfo($n);
			$ext = strtolower($tmpn["extension"]);
			$account = SessionManager::getAccount();
// 			$folder = PROJ_WORK_DIR.$account."/addr_list/";
// 			_createFolder($folder);
// 			move_uploaded_file( $file["tmp_name"],$folder.$name);
// 			file_put_contents($folder.$name, $data);
// 			$count = CommonUtils::getLineCountOfFile($folder.$name, true);
			
			$am = new AddrsManager($account);
			
			$db_idx = _get_db_index($am);
// 			Log::debug(__METHOD__.' @ db_index: '.$db_idx);

			$count = _upload_list($file['tmp_name'], $db_idx, $account);
			
			$addArray = array(
				'addrlist_name' => $am->sql_escape_string($lname),
				'addrlist_alias' => $am->sql_escape_string($als),
// 				存储 sqlite db index
				'addrlist_file' => $am->sql_escape_string($db_idx),
				'addrs_sum' => $count,
				'description' => $am->sql_escape_string($des)
			);
			Log::oper_log($account.LOG_SEP.'add addressList:'.LOG_SEP.json_encode($addArray));
			$res = $am->add($addArray);
			if($res===null) {
				$res = 9;
			}
			echo '{"code":', $res,'}';
		} else {
			echo '{"code": 99}';
		}
	}
	exit;
}
//校验上传文件类型  txt csv
function _checkType($type) {
// 	Log::debug($t . " - " . (strpos($type, 'text')));
// 	Log::debug($t . " - " . (strpos($type, 'excel')));
	return (!(strpos($type, 'text')===false)||!(strpos($type, 'excel')===false));
}

function _createFolder($fileFolder) {
	if(!file_exists($fileFolder)) {
		mkdir($fileFolder, 0777, true);
	}
}

//计数并检查上限，逐行处理上传列表到sqlite
function _upload_list($file_path, $db_idx=0, $account) {
	$line = 0 ;
	$param = array();
	$fp = fopen($file_path , 'r') or die("open file failure!");
	if($fp) {
		try{
			$alm = new AddrListManager($account, $db_idx);
		} catch(Exception $e){
			echo '{"code": 97}';
			exit;
		}
		
		while ( !feof ( $fp) ) {
			$lf=stream_get_line($fp,8192,"\r\n");
			if(($len=strlen($lf)) > 0) {
				$lf = CommonUtils::str_conv_to_utf8($lf);
				$info = "";
				if(($idx=strpos($lf,',') )> 0) {
					$info = substr($lf, $idx + 1);
					$lf = substr($lf, 0, $idx);
// 					Log::debug(__METHOD__.' # '.LOG_SEP.strlen($lf) .LOG_SEP.' idx '.$idx.LOG_SEP.substr($lf, 0, $idx));
// 					Log::debug(__METHOD__.' # '.LOG_SEP.strlen($lf) .LOG_SEP.' '.substr($lf, $idx + 1));
				}
				$param[$lf] = $info;
				$line ++;
			}
		}
		fclose($fp);
	}
	
	if($line==0) {
		echo '{"code": 90}';		//文件为空
		exit;
	}
	if(!_checkLimit($line)) {
		echo '{"code": 88}';		//条数超额
		exit;
	}
	
// 	Log::oper_log($account.LOG_SEP.'_upload_list:'.LOG_SEP.json_encode($param));
	$res = $alm->add($param);
	Log::debug(__METHOD__.' # '.LOG_SEP.' file count:  '.$line.LOG_SEP.'upload count: '.json_encode($res));
	return $line;
}
function download() {
	$idx = $_GET['file'];
	if(empty($idx)===true&&$idx!=0) {
		exit;
	}
	$account = SessionManager::getAccount();
	
	$alm = new AddrListManager($account, $idx);
	$res = $alm->query();
	if(!empty($res)) {
		$filecontent = "";
		foreach($res as $key=>$val){
			$info = $val["info"];
			$filecontent .= $val["email"];
			if(strlen($info) > 0) {
				$filecontent.= ",".$info;
			}
			$filecontent .="\r\n";
		}
		try{
			$filename = CommonUtils::generate_random_name().'.txt';
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".$filename);
			echo $filecontent;
		}catch(Exception $e) {
			Log::error('Caught exception: '.__METHOD__.' @ '. $account . '   ' . $idx . '  '. $e->getMessage());
		}
	}
	Log::debug(__METHOD__.' @'.__LINE__.' db: '.json_encode($res));
	
}

function template() {
	$type = $_GET['t'];
	if(empty($type)){
		echo array("code"=>0);
		exit;
	}
	$filename = PATH_PROJ_WEB."download/tpl/addrlist.".$type;
	if(file_exists($filename)) {
		try{
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".basename($filename));
			echo file_get_contents($filename);
		}catch(Exception $e) {
			Log::error('Caught exception: '.__METHOD__.' @ '. $filename . '   ' . $e->getMessage());
		}
	}
}

//查询 列表文件存储的 db index
function _get_db_index(AddrsManager $am) {
	$res = $am->query_db();
	$idx = 0;
	if(!empty($res)) {
		foreach($res as $key=>$val) {
	// 		Log::debug(__METHOD__.' @ '.__LINE__.LOG_SEP.$key.' == '.$idx.' == '.$val['idx']);
			if($idx!=$val['idx']) {
				return $idx;
			}
			$idx ++;
		}
	}
	return $idx;
}
?>
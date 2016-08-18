<?php
require_once __DIR__.'/../common/simple_fastdfs_client.php';
require_once __DIR__."/../../../include/login_filter_include.php";

//获取邮件列表并分页
function getList() {
	$account = SessionManager::getAccount();
	
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$size = isset($_POST['size']) ? $_POST['size'] : '';
	$listName = isset($_POST['list']) ? $_POST['list'] : "replies";
	
	if(empty($page) || empty($size)){
		echo '{"code": 0}';
		exit;
	}
	
	$limit_start = ($page-1)*$size;
	/* $dataList = $tm->query_by_page(array("order" => array("create_time DESC"), "limit" => array($limit_start, $size)));
	if(empty($dataList)){
		echo '{"code": 900}';
		exit;
	} */
	
	$data = array("data"=>$dataList);
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($data);
	//Log::debug(json_encode($data));
	//echo '{"data":{"result":[{"from":"zzz@send.com","time":"1460082185","subject":"test 222"},{"from":"zzz@send.com","time":"1460082185","subject":"test 222"}],"total":"2"},"orderNum":"10"}';  
}

//回复显示
function replyShow(){
	$account = SessionManager::getAccount();
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	
	$data = array("data"=>$dataList);
	header("Content-Type:text/html; charset=utf-8");
	echo json_encode($data);
}

//回复
function reply(){
	header("Content-Type:text/html; charset=utf-8");
	
	$content = isset($_POST['content']) ? $_POST['content'] : '';
	$subject = "";
	
	//$to调接口，返回订阅我的人
	$to = "zzz@send.com,test@send.com";
	if(empty($userPreview)){
		$to = $from;
	}
	
	if(empty($from) || empty($to)){
		echo '{"success": "false", "msg": "from ='.$from.' or to='.$to.' is invalid."}';
		exit;
	}
	
	$fname = CommonUtils::generate_random_name();
	$folder = PROJ_WORK_DIR.$from."/template/".$fname;
	createFolder($folder);
	
	$fileTmp = $folder."/mail";
	file_put_contents($folder."/mail", $content);
	
	//存信体
	$indexPath = uploadFile($fileTmp);
	if(isFileExists($indexPath) !== 1){
		echo '{"code":920}';
		exit;
	}
	
	$tm = new TemplatesManager($from);
	$res = $tm->add(array(
			'tpl_name' => $tplName,
			'tpl_file' => $indexUrl,
			'subject' => $tplSubject,
			'status' => 0
	));
	
	if ($res === null) {
		$res = 902;
	}
	
	delTplAll($folder);
	
	if($res != 200){
		echo '{"code":'.$res.'}';
	}
	
	Log::oper_log($account.LOG_SEP.'Send Mail '.LOG_SEP.$tplName.LOG_SEP.$fname.LOG_SEP.$folder.LOG_SEP.$res);
}

function delMail(){
	header("Content-Type:text/html; charset=utf-8");
	
	$content = isset($_POST['content']) ? $_POST['content'] : '';
	$flag = isset($_POST['flag']) ? $_POST['flag'] : '';
	
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

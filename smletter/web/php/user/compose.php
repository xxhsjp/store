<?php
require_once __DIR__.'/../common/simple_fastdfs_client.php';
require_once __DIR__."/../../../include/login_filter_include.php";

//信体放到fastdfs上，再往sqlite插一条记录
function send(){
	header("Content-Type: application/json; charset=utf-8");
	
	$send_type = isset($_POST['send_type'])?$_POST['send_type']:''; //存草稿  预览
	$content = isset($_POST['content'])?$_POST['content']:'';
	$subject = isset($_POST['subject'])?$_POST['subject']:'';
	$userPreview = isset($_POST['previewFlag'])?$_POST['previewFlag']:'';
	$from = SessionManager::getAccount();
	
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

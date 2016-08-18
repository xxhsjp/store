<?php

$g_module_status = array(
	"unexposed" => false,
	"audit" => false,
	"admin" => false,	
	"user" => true,
	"common" => true
);


$g_module_mapping = array(
	PROJ_ROOT.'web/php/user/send.php' => "unexposed",
		
	PROJ_ROOT.'web/php/user/task.php' => "user",
	PROJ_ROOT.'web/php/user/addr.php' => "user",
	PROJ_ROOT.'web/php/user/template.php' => "user",
		
	PROJ_ROOT.'web/php/admin/tplManage.php' => "user",
		
	PROJ_ROOT.'web/php/user/compose.php' => "user",
	PROJ_ROOT.'web/php/user/mailList.php' => "user",
		
	PROJ_ROOT.'web/php/user/sent.php' => "user",	
	PROJ_ROOT.'web/php/user/subs.php' => "user",
			
	PROJ_ROOT.'web/php/user/setting.php' => "common",
	PROJ_ROOT.'web/php/common/load.php' => "common",
	PROJ_ROOT.'web/php/user/profile.php' => "common",
	PROJ_ROOT.'index.php' => "common"
);


?>

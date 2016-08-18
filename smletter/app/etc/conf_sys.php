<?php
//log output level 1|2|4|8
define('LOG_OUTPUT_LEVEL', 15);
//log save path
define('LOG_OUTPUI_PATH', __DIR__.'/../../log/');
//system log file mail_sys.log (set 'CLOSE' to close log)
define('LOG_OUTPUT_SYS_FILE', 'sml_sys.log');
//operation log file mail_oper.log (set 'CLOSE' to close log)
define('LOG_OUTPUT_OPER_FILE', 'sml_oper.log');
//sqlite log file mail_sqlite.log (set 'CLOSE' to close log)
define('LOG_OUTPUT_SQLITE_FILE', 'CLOSE');
//login log file mail_login.log (set 'CLOSE' to close log)
define('LOG_OUTPUT_LOGIN_FILE', 'sml_login.log');
//data log file mail_data.log (set 'CLOSE' to close log)
define('LOG_OUTPUT_DATA_FILE', 'sml_data.log');

define('LOG_SEP', ' -- ');

//custom set
define('PROJ_NAME', '邮件自媒体');
define('PROJ_ROOT', '/smletter/');
define('PROJ_COOKIE_PREFIX', 'smletter');
define('PROJ_WORK_DIR', PATH_PROJ.'work/');
define('PATH_PROJ_DATA', PATH_PROJ.'data/');
define('PATH_PROJ_TPL', PATH_PROJ.'tpl/');

define('TASK_SYS_ACCOUNT', 'system@send.com');
//模板审批
define('TEMPLATE_AUDIT', 'admin@send.com');

//上传文件大小
define('UPLOAD_FILE_SIZE','100');

//上传模板包ZIP大小
define('UPLOAD_ZIP_SIZE','1024');

//default language
define('DEFAULT_LANG', 'CN');
define('DEFAULT_SKIN', 'COOL');

//curl timeout second
define('CURL_TIMEOUT_SEC', 5);

//curl timeout second
define('DEFAULT_DOMAIN', 'smletter.com');
?>

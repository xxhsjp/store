<?php
require_once PATH_PROJ_ETC."conf_sqlite.php";

class SQLiter
{
	const TYPE_USER = "user";
    const TYPE_USER_DELETE = "user_delete";
    const TYPE_DOMAIN = "domain";
    const TYPE_DOMAIN_DELETE = "domain_delete";
    const SQL_TYPE_SELECT = "select";
    const SQL_TYPE_EXECUTE = "execute";
    const SQL_TYPE_NULL = "null";
    const PREFIX_POST_JSON = "JSON=";

	public static function getServer($hashId){
		return constant('SQLITESERVER_'.$hashId);
	}

	public static function getDBType($db_name){
// 		$user_type = explode(",", trim(constant('SQLITE_DB_USER')));
		$domain_type = explode(",", trim(constant('SQLITE_DB_DOMAIN')));
// 		if(in_array($db_name, $user_type)){
// 			return self::TYPE_USER;
// 		}
		if(in_array($db_name, $domain_type)){
			return self::TYPE_DOMAIN;
		}else {
			return self::TYPE_USER;
		}
// 		return 'NO_EXIST_DB';
	}
    
	public static function getReConnTimes(){
		return constant('SQLITE_RECONN_TIMES');
	}
	
	public static function getTimeoutSec(){
		return constant('SQLITE_TIMEOUT_SEC');
	}

}

?>